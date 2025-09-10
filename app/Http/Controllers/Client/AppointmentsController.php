<?php

namespace App\Http\Controllers\Client;

use App\Models\Clients\Appointment;
use App\Models\Clients\Client;
use App\Models\Clients\Product;
use App\Models\Clients\Sale;
use App\Models\Clients\SaleItem;
use App\Models\Clients\Service;
use App\Models\Clients\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $viewType = $request->get('view', 'list'); // list или calendar

        // Базовый запрос для записей
        $appointmentsQuery = Appointment::with([
                'client', 
                'service', 
                'user',
                'sales.items.product', // Загружаем продажи с товарами
                'childAppointments.service', // Загружаем дочерние записи
                'parentAppointment.service' // Загружаем родительскую запись
            ])
            ->where('project_id', $currentProjectId);

        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $appointmentsQuery->where('user_id', $currentUser->id);
        }

        $appointments = $appointmentsQuery
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(11);

        // Добавляем total_amount для каждой записи
        $appointments->getCollection()->transform(function ($appointment) {
            $appointment->total_amount = $appointment->getTotalAmountAttribute();
            return $appointment;
        });

        $clients = Client::where('project_id', $currentProjectId)->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'instagram' => $client->instagram,
                'email' => $client->email,
                'phone' => $client->phone,
            ];
        });

        $services = Service::where('project_id', $currentProjectId)->get();
        $products = Product::with('warehouse')
            ->where('project_id', $currentProjectId)
            ->whereHas('warehouse', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->warehouse->retail_price,
                    'purchase_price' => $product->warehouse->purchase_price,
                    'quantity' => $product->warehouse->quantity
                ];
            });

        $users = User::select('id', 'name', 'role', 'project_id')
            ->where('project_id', $currentProjectId)
            ->get();

        // Отладочная информация
        \Log::info('Users for project ' . $currentProjectId . ':', $users->toArray());

        return view('client.appointments.list', compact(
            'appointments',
            'clients',
            'services',
            'viewType',
            'products',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'service_id' => 'required|exists:services,id',
                'date' => 'required|date',
                'time' => 'required',
                'price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'status' => 'nullable|string|in:pending,completed,cancelled,rescheduled',
                'user_id' => 'required|exists:admin_users,id',
                'duration_hours' => 'nullable|integer|min:0',
                'duration_minutes' => 'nullable|integer|min:0|max:59',
            ]);

            $duration = ($validated['duration_hours'] ?? 0) * 60 + ($validated['duration_minutes'] ?? 0);
            
            if ($duration === 0) {
                $service = Service::find($validated['service_id']);
                $duration = $service->duration ?? 60;
            }
            
            $appointmentData = $validated + [
                'project_id' => $currentProjectId,
                'duration' => $duration
            ];

            $appointment = Appointment::create($appointmentData);

            // Отправляем уведомление в Telegram
            try {
                $appointmentData = [
                    'client_name' => $appointment->client->name,
                    'client_phone' => $appointment->client->phone,
                    'client_email' => $appointment->client->email,
                    'service_name' => $appointment->service->name,
                    'master_name' => $appointment->user ? $appointment->user->name : 'Не назначен',
                    'date' => $appointment->date,
                    'time' => $appointment->time,
                    'price' => $appointment->price,
                    'notes' => $appointment->notes ?? 'Запись создана через админку'
                ];

                \App\Jobs\SendTelegramNotification::dispatch($appointmentData, $currentProjectId);
                
                \Log::info('Telegram notification job dispatched for admin appointment', [
                    'project_id' => $currentProjectId,
                    'appointment_id' => $appointment->id
                ]);
            } catch (\Exception $e) {
                \Log::error('Error dispatching Telegram notification for admin appointment: ' . $e->getMessage());
                // Не прерываем выполнение, если Telegram уведомление не отправилось
            }

            return response()->json([
                'success' => true,
                'appointment' => $appointment->load(['client', 'service', 'user'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'time' => 'required',
                'client_id' => 'required|exists:clients,id',
                'service_id' => 'required|exists:services,id',
                'price' => 'required|numeric|min:0',
                'status' => 'nullable|string|in:pending,completed,cancelled,rescheduled',
                'sales' => 'array',
                'sales.*.product_id' => 'required|exists:products,id',
                'sales.*.quantity' => 'required|integer|min:1',
                'sales.*.price' => 'required|numeric|min:0',
                'sales.*.purchase_price' => 'required|numeric|min:0',
                'user_id' => 'nullable|exists:admin_users,id',
                'duration_hours' => 'nullable|integer|min:0',
                'duration_minutes' => 'nullable|integer|min:0|max:59',
            ]);

            DB::beginTransaction();

            try {
                $query = Appointment::with(['sales.items'])->where('project_id', auth()->user()->project_id);
                
                // Если пользователь не админ, проверяем что это его запись
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $query->where('user_id', $currentUser->id);
                }
                
                $appointment = $query->findOrFail($id);
                $oldClientId = $appointment->client_id;
                
                $duration = ($validated['duration_hours'] ?? 0) * 60 + ($validated['duration_minutes'] ?? 0);
                if ($duration === 0) {
                    $service = Service::find($validated['service_id']);
                    $duration = $service->duration ?? 60;
                }

                $appointment->update([
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                    'client_id' => $validated['client_id'],
                    'service_id' => $validated['service_id'],
                    'price' => $validated['price'],
                    'status' => $validated['status'] ?? $appointment->status,
                    'user_id' => $validated['user_id'] ?? null,
                    'duration' => $duration,
                ]);

                // Если клиент изменился, обновляем client_id во всех связанных продажах
                if ($oldClientId != $validated['client_id']) {
                    foreach ($appointment->sales as $sale) {
                        $sale->update(['client_id' => $validated['client_id']]);
                    }
                }

                if ($request->has('sales')) {
                    $salesData = $validated['sales'] ?? [];
                    // --- Удаляем старые продажи и позиции ---
                    foreach ($appointment->sales as $sale) {
                        $sale->items()->delete();
                        $sale->delete();
                    }
                    // --- Создаём новую продажу, если есть товары ---
                    if (!empty($salesData)) {
                        $sale = Sale::create([
                            'appointment_id' => $appointment->id,
                            'client_id' => $appointment->client_id,
                            'employee_id' => $appointment->user_id, // Добавляем мастера из записи
                            'date' => $appointment->date,
                            'total_amount' => 0,
                            'project_id' => auth()->user()->project_id
                        ]);
                        $totalAmount = 0;
                        foreach ($salesData as $saleData) {
                            $itemTotal = $saleData['quantity'] * $saleData['price'];
                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $saleData['product_id'],
                                'quantity' => $saleData['quantity'],
                                'retail_price' => $saleData['price'],
                                'wholesale_price' => $saleData['purchase_price'],
                                'total' => $itemTotal,
                                'project_id' => $sale->project_id,
                            ]);
                            $totalAmount += $itemTotal;
                        }
                        $sale->update(['total_amount' => $totalAmount]);
                        
                        // Обновляем сумму записи, включив стоимость товаров
                        $appointment->update(['price' => $appointment->price + $totalAmount]);
                    }
                }

                // Обновляем дату, время и статус во всех дочерних записях
                $childAppointments = Appointment::where('parent_appointment_id', $appointment->id)->get();
                foreach ($childAppointments as $child) {
                    $child->update([
                        'date' => $validated['date'],
                        'time' => $validated['time'],
                        'client_id' => $validated['client_id'], // Также обновляем клиента
                        'user_id' => $validated['user_id'] ?? $child->user_id, // Обновляем мастера если указан
                        'status' => $validated['status'] ?? $appointment->status, // Синхронизируем статус
                    ]);
                }

                DB::commit();

                // Загружаем запись заново с всеми связями
                $appointment = Appointment::with([
                    'client', 
                    'service', 
                    'sales.items.product.warehouse', 
                    'user',
                    'childAppointments.service',
                    'childAppointments.sales.items.product'
                ])->find($appointment->id);
                
                // Принудительно загружаем дочерние записи
                $appointment->load('childAppointments.service');
                
                
                // Форматируем дату для правильного отображения
                $appointment->date_formatted = $appointment->date->format('d.m.Y');
                $appointment->date_html = $appointment->date->format('Y-m-d');
                
                // Рассчитываем общую стоимость
                $appointment->total_amount = $appointment->getTotalAmountAttribute();
                
                
                $products = Product::with('warehouse')
                    ->whereHas('warehouse', function($query) {
                        $query->where('quantity', '>', 0);
                    })
                    ->get()
                    ->map(function($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->warehouse->retail_price,
                            'purchase_price' => $product->warehouse->purchase_price,
                            'quantity' => $product->warehouse->quantity
                        ];
                    });


                return response()->json([
                    'success' => true,
                    'message' => 'Запись успешно обновлена',
                    'appointment' => $appointment,
                    'products' => $products
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        try {
            $query = Appointment::with('sales.items')->where('project_id', $currentProjectId);
            
            // Если пользователь не админ, проверяем что это его запись
            if ($currentUser->role !== 'admin') {
                $query->where('user_id', $currentUser->id);
            }
            
            $appointment = $query->find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => true, // Все равно считаем успехом, так как запись уже удалена
                    'message' => 'Запись не найдена (возможно, уже удалена)'
                ]);
            }

            DB::transaction(function () use ($appointment) {
                // Если это основная запись, удаляем все дочерние записи
                if (!$appointment->parent_appointment_id) {
                    $childAppointments = Appointment::where('parent_appointment_id', $appointment->id)->get();
                    foreach ($childAppointments as $child) {
                        // Возвращаем товары на склад для дочерних записей
                        foreach ($child->sales as $sale) {
                            foreach ($sale->items as $item) {
                                if ($item->product && $item->product->warehouse) {
                                    $item->product->warehouse->increment('quantity', $item->quantity);
                                }
                            }
                            $sale->items()->delete();
                        }
                        $child->sales()->delete();
                        $child->delete();
                    }
                }

                // Возвращаем товары на склад перед удалением
                foreach ($appointment->sales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($item->product && $item->product->warehouse) {
                            $item->product->warehouse->increment('quantity', $item->quantity);
                        }
                    }
                    $sale->items()->delete();
                }

                $appointment->sales()->delete();
                $appointment->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Запись успешно удалена'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Appointment $appointment)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его запись
        if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        try {
            $appointmentData = $appointment->load(['client', 'service', 'user']);
            
            // Определяем длительность: сначала из записи, если нет - из услуги
            $duration = $appointment->duration ?? $appointment->service->duration ?? 0;

            // Добавляем вычисление часов и минут для формы
            $appointmentData->duration_hours = floor($duration / 60);
            $appointmentData->duration_minutes = $duration % 60;
            
            // Форматируем дату для правильного отображения в JavaScript
            $appointmentData->date_formatted = $appointmentData->date->format('d.m.Y');
            $appointmentData->date_html = $appointmentData->date->format('Y-m-d'); // Для HTML поля даты

            return response()->json([
                'success' => true,
                'appointment' => $appointmentData,
                'clients' => Client::where('project_id', $currentProjectId)->get(),
                'services' => Service::where('project_id', $currentProjectId)->get(),
                'users' => User::where('project_id', $currentProjectId)->get()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view($id)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        try {
            \Log::info('Appointment view - Starting', ['id' => $id, 'project_id' => $currentProjectId]);
            
            $query = Appointment::with(['client', 'service', 'sales.items.product', 'user', 'parentAppointment', 'childAppointments.service'])
                ->where('project_id', $currentProjectId);
            
            // Если пользователь не админ, показываем только его записи
            if ($currentUser->role !== 'admin') {
                $query->where('user_id', $currentUser->id);
            }
            
            $appointment = $query->findOrFail($id);
            \Log::info('Appointment found', ['appointment_id' => $appointment->id]);
            
            // Получаем все связанные записи (временно упрощаем для отладки)
            $relatedAppointments = collect([$appointment]);
            if ($appointment->parent_appointment_id) {
                // Если это дочерняя запись, загружаем родительскую и все дочерние
                $parent = Appointment::with(['service'])->find($appointment->parent_appointment_id);
                if ($parent) {
                    $children = Appointment::with(['service'])->where('parent_appointment_id', $parent->id)->get();
                    $relatedAppointments = $children->prepend($parent);
                }
            } else {
                // Если это основная запись, загружаем дочерние
                $children = Appointment::with(['service'])->where('parent_appointment_id', $appointment->id)->get();
                $relatedAppointments = $children->prepend($appointment);
            }
            \Log::info('Related appointments loaded', ['count' => $relatedAppointments->count()]);

            // Получаем только продажи, связанные с этой конкретной записью
            $saleItems = [];
            foreach ($appointment->sales as $sale) {
                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $saleItems[] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->retail_price,
                            'purchase_price' => $item->wholesale_price
                        ];
                    }
                }
            }

            // Форматируем дату для правильного отображения в JavaScript
            $appointment->date_formatted = $appointment->date->format('d.m.Y');
            $appointment->date_html = $appointment->date->format('Y-m-d'); // Для HTML поля даты
            
            // Форматируем даты для всех связанных записей
            $relatedAppointments = $relatedAppointments->map(function($apt) {
                $apt->date_formatted = $apt->date->format('d.m.Y');
                $apt->date_html = $apt->date->format('Y-m-d'); // Для HTML поля даты
                return $apt;
            });
            
            return response()->json([
                'success' => true,
                'appointment' => $appointment,
                'relatedAppointments' => $relatedAppointments,
                'sales' => $saleItems,
                'products' => Product::with('warehouse')
                    ->whereHas('warehouse', function($query) {
                        $query->where('quantity', '>', 0);
                    })
                    ->get()
                    ->map(function($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->warehouse->retail_price,
                            'purchase_price' => $product->warehouse->purchase_price,
                            'quantity' => $product->warehouse->quantity
                        ];
                    })
            ]);

        } catch (\Exception $e) {
            \Log::error('Appointment view error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addProduct(Request $request, $appointmentId)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'purchase_price' => 'required|numeric|min:0'
            ]);

            return DB::transaction(function () use ($validated, $appointmentId, $currentProjectId) {
                $query = Appointment::where('project_id', $currentProjectId);
                
                // Если пользователь не админ, проверяем что это его запись
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $query->where('user_id', $currentUser->id);
                }
                
                $appointment = $query->findOrFail($appointmentId);
                $product = Product::with('warehouse')->where('project_id', $currentProjectId)->findOrFail($validated['product_id']);

                // Проверка наличия товара
                if (!$product->warehouse || $product->warehouse->quantity < $validated['quantity']) {
                    throw new \Exception('Недостаточно товара на складе');
                }

                // Находим или создаем продажу
                $sale = Sale::firstOrCreate(
                    ['appointment_id' => $appointmentId],
                    [
                        'client_id' => $appointment->client_id,
                        'employee_id' => $appointment->user_id, // Добавляем мастера из записи
                        'date' => $appointment->date,
                        'total_amount' => 0,
                        'project_id' => $currentProjectId
                    ]
                );

                // Проверяем, есть ли уже такой товар в продаже
                $existingItem = $sale->items()->where('product_id', $validated['product_id'])->first();
                
                if ($existingItem) {
                    // Если товар уже есть, обновляем количество
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $validated['quantity'],
                        'total' => ($existingItem->quantity + $validated['quantity']) * $validated['price']
                    ]);
                    $saleItem = $existingItem;
                } else {
                    // Создаем новую позицию в продаже
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $validated['product_id'],
                        'quantity' => $validated['quantity'],
                        'retail_price' => $validated['price'],
                        'wholesale_price' => $validated['purchase_price'],
                        'total' => $validated['quantity'] * $validated['price'],
                        'project_id' => $sale->project_id,
                    ]);
                }

                // Обновляем общую сумму продажи
                if ($existingItem) {
                    // Пересчитываем общую сумму продажи
                    $totalAmount = $sale->items->sum('total');
                    $sale->update(['total_amount' => $totalAmount]);
                } else {
                    $sale->increment('total_amount', $saleItem->total);
                }

                // Уменьшаем количество на складе
                $product->warehouse->decrement('quantity', $validated['quantity']);

                // Получаем обновленные данные записи
                $appointment = $appointment->fresh(['client', 'service']);

                // Получаем все продажи для этого клиента на эту дату
                $currentUser = auth()->user();
                $salesQuery = Sale::with(['items.product'])
                    ->where('client_id', $appointment->client_id)
                    ->whereDate('date', $appointment->date);
                
                // Если пользователь не админ, показываем только его продажи
                if ($currentUser->role !== 'admin') {
                    $salesQuery->where('employee_id', $currentUser->id);
                }
                
                $sales = $salesQuery->get();

                // Формируем список товаров для отображения
                $saleItems = [];
                foreach ($sales as $s) {
                    foreach ($s->items as $item) {
                        if ($item->product) {
                            $saleItems[] = [
                                'product_id' => $item->product_id,
                                'name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'price' => $item->retail_price,
                                'purchase_price' => $item->wholesale_price
                            ];
                        }
                    }
                }

                // Получаем обновленный список доступных товаров
                $products = Product::with('warehouse')
                    ->whereHas('warehouse', function($query) {
                        $query->where('quantity', '>', 0);
                    })
                    ->get()
                    ->map(function($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->warehouse->retail_price,
                            'purchase_price' => $product->warehouse->purchase_price,
                            'quantity' => $product->warehouse->quantity
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'message' => 'Товар успешно добавлен',
                    'appointment' => $appointment,
                    'sales' => $saleItems,
                    'products' => $products
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($appointmentId, $saleId)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        try {
            return DB::transaction(function () use ($appointmentId, $saleId, $currentProjectId, $currentUser) {
                // Проверяем доступ к записи
                $appointment = Appointment::where('project_id', $currentProjectId)
                    ->where('id', $appointmentId)
                    ->first();
                
                if (!$appointment) {
                    return response()->json(['success' => false, 'message' => 'Запись не найдена'], 404);
                }
                
                // Если пользователь не админ, проверяем что это его запись
                if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
                }
                
                $sale = Sale::where('appointment_id', $appointmentId)
                    ->where('project_id', $currentProjectId)
                    ->findOrFail($saleId);

                // Возвращаем товар на склад
                foreach ($sale->items as $item) {
                    if ($item->product && $item->product->warehouse) {
                        $item->product->warehouse->increment('quantity', $item->quantity);
                    }
                    $item->delete();
                }

                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Товар успешно удален из записи'
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении товара: ' . $e->getMessage()
            ], 500);
        }
    }
    public function addProcedure(Request $request, Appointment $appointment)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его запись
        if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'price' => 'required|numeric|min:0',
            ]);

            // Определяем родительскую запись
            $parentAppointmentId = $appointment->isMainAppointment() 
                ? $appointment->id 
                : $appointment->parent_appointment_id;

            \Log::info('Creating new appointment', [
                'parent_date' => $appointment->date,
                'parent_date_formatted' => $appointment->date->format('Y-m-d'),
                'parent_time' => $appointment->time,
                'parent_id' => $appointment->id
            ]);

            // Создаем новую дату, копируя только дату без времени
            $appointmentDate = $appointment->date->startOfDay();
            
            $newAppointment = Appointment::create([
                'client_id' => $appointment->client_id,
                'service_id' => $validated['service_id'],
                'date' => $appointmentDate,
                'time' => $appointment->time,
                'price' => $validated['price'],
                'notes' => 'Добавлено через просмотр записи',
                'project_id' => $currentProjectId,
                'user_id' => $appointment->user_id,
                'parent_appointment_id' => $parentAppointmentId
            ]);

            \Log::info('New appointment created', [
                'parent_date' => $appointment->date,
                'parent_date_formatted' => $appointment->date->format('Y-m-d'),
                'new_date' => $newAppointment->date,
                'new_date_formatted' => $newAppointment->date->format('Y-m-d'),
                'new_time' => $newAppointment->time,
                'new_id' => $newAppointment->id
            ]);

            return response()->json([
                'success' => true,
                'appointment' => $newAppointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении услуги: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeProduct(Appointment $appointment, Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его запись
        if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        // Логика удаления товара из записи
        $appointment->products()->detach($validated['product_id']);

        return response()->json(['success' => true]);
    }

    public function calendarEvents(Request $request)
    {
        \Log::info('Calendar events method called', [
            'start' => $request->start,
            'end' => $request->end,
            'user_id' => auth()->user()->id
        ]);
        
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        try {
            $query = Appointment::with(['client', 'service', 'childAppointments.service'])
                ->where('project_id', $currentProjectId)
                ->whereNull('parent_appointment_id') // Показываем только основные записи
                ->where('parent_appointment_id', null); // Дополнительная проверка
            
            // Если пользователь не админ, показываем только его записи
            if ($currentUser->role !== 'admin') {
                $query->where('user_id', $currentUser->id);
            }
            
            $appointments = $query
                ->when($request->start, function($query) use ($request) {
                    // Очищаем дату от лишних символов
                    $startDate = $request->start;
                    $startDate = preg_replace('/[T ]\d{2}:\d{2}:\d{2}.*$/', '', $startDate);
                    return $query->whereDate('date', '>=', Carbon::parse($startDate));
                })
                ->when($request->end, function($query) use ($request) {
                    // Очищаем дату от лишних символов
                    $endDate = $request->end;
                    $endDate = preg_replace('/[T ]\d{2}:\d{2}:\d{2}.*$/', '', $endDate);
                    return $query->whereDate('date', '<=', Carbon::parse($endDate));
                })
                ->get();

            // Отладка
            \Log::info('Calendar events debug:', [
                'appointments_count' => $appointments->count(),
                'start_filter' => $request->start,
                'end_filter' => $request->end,
                'appointments' => $appointments->map(function($app) {
                    return [
                        'id' => $app->id,
                        'service' => $app->service->name,
                        'date' => $app->date,
                        'childAppointments_count' => $app->childAppointments ? $app->childAppointments->count() : 0
                    ];
                })->toArray()
            ]);

            $events = $appointments->map(function($appointment) {
                try {
                    // Правильное форматирование даты и времени
                    $date = Carbon::parse($appointment->date)->format('Y-m-d');
                    
                    // Очищаем время от лишних символов
                    $time = $appointment->time;
                    if ($time) {
                        // Убираем дату из времени, если она есть
                        $time = preg_replace('/^\d{4}-\d{2}-\d{2}T/', '', $time);
                        // Убираем timezone offset
                        $time = preg_replace('/[+-]\d{2}:\d{2}$/', '', $time);
                        // Убираем пробелы
                        $time = trim($time);
                        // Если время пустое или невалидное, используем по умолчанию
                        if (empty($time) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                            $time = '00:00:00';
                        }
                    } else {
                        $time = '00:00:00';
                    }
                    
                    $startDateTime = Carbon::parse($date . ' ' . $time);
                    $duration = $appointment->duration ?? $appointment->service->duration ?? 60;
                    $endDateTime = $startDateTime->copy()->addMinutes($duration);

                    // Собираем все услуги (основная + дочерние)
                    $allServices = collect([$appointment->service->name]);
                    $totalPrice = $appointment->price;
                    
                    if ($appointment->childAppointments && $appointment->childAppointments->count() > 0) {
                        foreach ($appointment->childAppointments as $child) {
                            $allServices->push($child->service->name);
                            $totalPrice += $child->price;
                        }
                    }
                    
                    $servicesText = $allServices->join(' + ');
                    
                    // Определяем классы - добавляем has-children только если есть дочерние записи
                    $classNames = ['status-' . $appointment->status];
                    if ($appointment->childAppointments && $appointment->childAppointments->count() > 0) {
                        $classNames[] = 'has-children';
                    }
                    
                    $event = [
                        'id' => $appointment->id,
                        'title' => $servicesText,
                        'start' => $startDateTime->format('Y-m-d\TH:i:s'),
                        'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                        'date' => Carbon::parse($date)->format('d.m.Y'),
                        'date_formatted' => Carbon::parse($date)->format('d.m.Y'),
                        'classNames' => $classNames,
                        'extendedProps' => [
                            'client' => $appointment->client->name,
                            'service' => $appointment->service->name,
                            'services' => $allServices->toArray(),
                            'price' => $totalPrice,
                            'notes' => $appointment->notes,
                            'status' => $appointment->status,
                            'time' => $appointment->time,
                            'duration' => $duration,
                            'title_week' => $startDateTime->format('H:i') . ' ' . $servicesText,
                            'title_day' => $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i') . ' ' . $servicesText . ' ' . $appointment->client->name,
                            'hasChildren' => $appointment->childAppointments->count() > 0,
                            'childrenCount' => $appointment->childAppointments->count()
                        ]
                    ];

                    return $event;

                } catch (\Exception $e) {
                    return null;
                }
            })->filter()->values();

            return response()->json($events);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка при загрузке событий: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEvents()
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $query = Appointment::with(['client', 'service'])->where('project_id', $currentProjectId);
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }
        
        $appointments = $query->get();

        $events = $appointments->map(function($appointment) {
            $start = $appointment->date->format('Y-m-d') . ' ' . $appointment->time;

            // Предполагаем, что услуга длится 1 час, если не указано иное
            $end = date('Y-m-d H:i:s', strtotime($start . ' +1 hour'));

            return [
                'id' => $appointment->id,
                'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                'start' => $start,
                'end' => $end,
                'className' => 'bg-info',
                'description' => $appointment->notes,
                'extendedProps' => [
                    'client_id' => $appointment->client_id,
                    'service_id' => $appointment->service_id,
                    'price' => $appointment->price,
                    'notes' => $appointment->notes
                ]
            ];
        });

        return response()->json($events);
    }

    public function show($id)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        try {
            $query = Appointment::with(['client', 'service', 'sales.items.product.warehouse'])
                ->where('project_id', $currentProjectId);
            
            // Если пользователь не админ, показываем только его записи
            if ($currentUser->role !== 'admin') {
                $query->where('user_id', $currentUser->id);
            }
            
            $appointment = $query->findOrFail($id);

            return response()->json([
                'success' => true,
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке записи: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSales(Request $request, Appointment $appointment)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его запись
        if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        // Проверка и сохранение данных
        $validatedData = $request->validate([
            'total_price' => 'required|numeric',
            'products' => 'array'
        ]);

        $appointment->price = $validatedData['total_price'];
        $appointment->save();

        // Логика для обновления или создания продаж связанных с продуктами (если есть)

        return response()->json(['success' => true]);
    }

    /**
     * Получение данных о статусах записей для отчета.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentStatusData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }

        $query = Appointment::where('project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Если пользователь не админ, показываем только его записи
        $currentUser = auth()->user();
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }
        
        $statuses = $query
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();
        
        $statusLabels = [
            'completed' => __('messages.completed'),
            'cancelled' => __('messages.cancelled'),
            'pending' => __('messages.pending'),
            'rescheduled' => __('messages.rescheduled'),
        ];
        
        $labels = [];
        $data = [];
        $backgroundColors = [
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            'pending' => '#f59e0b',
            'rescheduled' => '#3b82f6',
        ];
        $colors = [];

        foreach ($statuses as $status => $count) {
            if (isset($statusLabels[$status])) {
                $labels[] = $statusLabels[$status];
                $data[] = $count;
                $colors[] = $backgroundColors[$status] ?? '#6b7280';
            }
        }
        
        if (empty($labels)) {
            return response()->json([
                'labels' => [__('messages.no_data')],
                'data' => [0],
                'colors' => ['#d1d5db']
            ]);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ]);
    }

    /**
     * Получение данных о популярности услуг для отчета.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicePopularityData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }

        $query = Appointment::where('appointments.project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Если пользователь не админ, показываем только его записи
        $currentUser = auth()->user();
        if ($currentUser->role !== 'admin') {
            $query->where('appointments.user_id', $currentUser->id);
        }
        
        $servicePopularity = $query
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(appointments.id) as count'))
            ->groupBy('services.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'name');

        if ($servicePopularity->isEmpty()) {
            return response()->json([
                'labels' => [__('messages.no_data')],
                'data' => [0]
            ]);
        }

        return response()->json([
            'labels' => $servicePopularity->keys(),
            'data' => $servicePopularity->values()
        ]);
    }

    /**
     * Предоставляет данные о количестве записей по дням, неделям или месяцам.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentsByDay(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            $period = 'custom';
        } else {
            $period = $request->get('period', 'week');
            $today = Carbon::today();
            switch ($period) {
                case 'week':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $today->copy()->startOfWeek()->subWeek();
                    $endDate = $today->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $today->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $today->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $today->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $today->copy()->endOfDay();
                    break;
                default:
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfDay();
                    break;
            }
        }

        $periodRange = [$startDate, $endDate];

        switch ($period) {
            case 'half_year': // Группировка по неделям, только с данными
                $dateGroupRaw = 'YEARWEEK(date, 1)';
                $query = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange);
                
                // Если пользователь не админ, показываем только его записи
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $query->where('user_id', $currentUser->id);
                }
                
                $rawData = $query
                    ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get();
                $labels = $rawData->pluck('date_group')->map(function($week) {
                    $year = substr($week, 0, 4);
                    $w = (int)substr($week, 4);
                    $date = Carbon::now()->setISODate($year, $w)->startOfWeek();
                    return $date->format('d.m') . ' - ' . $date->copy()->endOfWeek()->format('d.m');
                });
                $values = $rawData->pluck('count');
                return response()->json(['labels' => $labels, 'data' => $values]);
            case 'year': // Группировка по месяцам, только с данными
                $dateGroupRaw = 'DATE_FORMAT(date, "%Y-%m")';
                $query = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange);
                
                // Если пользователь не админ, показываем только его записи
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $query->where('user_id', $currentUser->id);
                }
                
                $rawData = $query
                    ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get();
                $labels = $rawData->pluck('date_group')->map(fn($d) => Carbon::parse($d.'-01')->translatedFormat('M Y'));
                $values = $rawData->pluck('count');
                return response()->json(['labels' => $labels, 'data' => $values]);
            default: // 'week', '2weeks', 'month' — группировка по дням
                $dateGroupRaw = 'DATE(date)';
                $periodIterator = CarbonPeriod::create($periodRange[0], '1 day', $periodRange[1]);
                $dateGroupFormatter = fn(Carbon $date) => $date->format('Y-m-d');
                $labelFormatter = fn($dateGroup) => $dateGroup;
                $query = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange);
                
                // Если пользователь не админ, показываем только его записи
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $query->where('user_id', $currentUser->id);
                }
                
                $data = $query
                    ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date_group')
                    ->get()
                    ->keyBy('date_group');
                $scaffold = [];
                foreach ($periodIterator as $date) {
                    $key = $dateGroupFormatter($date);
                    $scaffold[$key] = $data->get($key)->count ?? 0;
                }
                $labels = collect(array_keys($scaffold))->map($labelFormatter);
                $values = array_values($scaffold);
                // Если все значения нули — возвращаем пустые массивы
                if (collect($values)->sum() === 0) {
                    return response()->json(['labels' => [], 'data' => []]);
                }
                return response()->json(['labels' => $labels, 'data' => $values]);
        }
    }

    /**
     * Топ-5 клиентов по выручке за период
     */
    public function getTopClientsByRevenue(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }
        $clients = Client::where('project_id', $currentProjectId)
            ->select('name', 'id')
            ->withSum(['appointments as revenue' => function($q) use ($startDate, $endDate, $currentProjectId) {
                $q->where('project_id', $currentProjectId)->whereBetween('date', [$startDate, $endDate]);
                
                // Если пользователь не админ, показываем только его записи
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $q->where('user_id', $currentUser->id);
                }
            }], 'price')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        $labels = $clients->pluck('name');
        $data = $clients->pluck('revenue')->map(function($v){return (float)$v;});
        return response()->json(['labels'=>$labels,'data'=>$data]);
    }

    /**
     * Динамика среднего чека по датам (только по дням, где есть данные)
     */
    public function getAvgCheckDynamics(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }
        $groupByRaw = 'date';
        $labelFormat = fn($d) => date('d.m', strtotime($d));
        if (isset($period)) {
            if ($period === 'half_year') {
                $groupByRaw = 'YEARWEEK(date, 1)';
                $labelFormat = function($week) {
                    $year = substr($week, 0, 4);
                    $w = (int)substr($week, 4);
                    $date = \Carbon\Carbon::now()->setISODate($year, $w)->startOfWeek();
                    return $date->format('d.m') . ' - ' . $date->copy()->endOfWeek()->format('d.m');
                };
            } elseif ($period === 'year') {
                $groupByRaw = 'DATE_FORMAT(date, "%Y-%m")';
                $labelFormat = function($month) {
                    return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('m.Y');
                };
            }
        }
        $query = Appointment::where('project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Если пользователь не админ, показываем только его записи
        $currentUser = auth()->user();
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }
        
        $checks = $query
            ->selectRaw($groupByRaw.' as period_group, AVG(price) as avg_check')
            ->groupBy('period_group')
            ->orderBy('period_group')
            ->get();
        $labels = $checks->pluck('period_group')->map($labelFormat);
        $data = $checks->pluck('avg_check')->map(fn($v)=>round($v,2));
        return response()->json(['labels'=>$labels,'data'=>$data]);
    }

    /**
     * LTV по типам клиентов
     */
    public function getLtvByClientType(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }
        $types = ClientType::with(['clients' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        }, 'clients.sales' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
            
            // Если пользователь не админ, показываем только его продажи
            $currentUser = auth()->user();
            if ($currentUser->role !== 'admin') {
                $q->where('employee_id', $currentUser->id);
            }
        }])->get();
        $labels = [];
        $data = [];
        foreach ($types as $type) {
            $labels[] = $type->name;
            $ltv = 0;
            foreach ($type->clients as $client) {
                $ltv += $client->sales->sum('total_amount');
            }
            $data[] = round($ltv,2);
        }
        return response()->json(['labels'=>$labels,'data'=>$data]);
    }

    /**
     * Топ-10 услуг по выручке за период
     */
    public function getTopServicesByRevenue(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case '2weeks':
                    $startDate = $endDate->copy()->startOfWeek()->subWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'half_year':
                    $sixMonthsAgo = $endDate->copy()->subMonths(6)->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $sixMonthsAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $sixMonthsAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                case 'year':
                    $yearAgo = $endDate->copy()->subYear()->startOfMonth();
                    $firstAppointment = Appointment::query()->where('date', '>=', $yearAgo)->orderBy('date')->first();
                    $startDate = $firstAppointment ? Carbon::parse($firstAppointment->date)->startOfMonth() : $yearAgo;
                    $endDate = $endDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $endDate->copy()->startOfWeek();
                    $endDate = $endDate->copy()->endOfDay();
                    break;
            }
        }
        $services = Service::select('name')
            ->withSum(['appointments as revenue' => function($q) use ($startDate, $endDate, $currentProjectId) {
                $q->where('appointments.project_id', $currentProjectId)->whereBetween('date', [$startDate, $endDate]);
                
                // Если пользователь не админ, показываем только его записи
                $currentUser = auth()->user();
                if ($currentUser->role !== 'admin') {
                    $q->where('appointments.user_id', $currentUser->id);
                }
            }], 'price')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();
        $labels = $services->pluck('name');
        $data = $services->pluck('revenue')->map(function($v){return (float)$v;});
        return response()->json(['labels'=>$labels,'data'=>$data]);
    }

    public function getClientAnalyticsData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } else {
            $period = $request->get('period', 'week');
            $endDate = Carbon::now();
            switch ($period) {
                case '2weeks': $startDate = $endDate->copy()->subWeeks(2); break;
                case 'month': $startDate = $endDate->copy()->subMonth(); break;
                case 'half_year': $startDate = $endDate->copy()->subMonths(6); break;
                case 'year': $startDate = $endDate->copy()->subYear(); break;
                case 'week': default: $startDate = $endDate->copy()->subWeek(); break;
            }
        }
        // ... остальной код, где используется $startDate, $endDate ...
    }

    public function ajax(Request $request)
    {
        try {
            $currentProjectId = auth()->user()->project_id;
            $currentUser = auth()->user();
            $perPage = (int)($request->get('per_page', 11));
            $search = $request->get('search');
            $query = Appointment::with(['client', 'service', 'user', 'childAppointments.service', 'sales'])
                ->where('project_id', $currentProjectId)
                ->whereNull('parent_appointment_id') // Показываем только основные записи
                ->where('parent_appointment_id', null); // Дополнительная проверка
            
            // Если пользователь не админ, показываем только его записи
            if ($currentUser->role !== 'admin') {
                $query->where('user_id', $currentUser->id);
            }
            
            if ($search) {
                $query->whereHas('client', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('instagram', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%")
                    ;
                });
            }
            $query->orderBy('date', 'desc')->orderBy('time', 'desc');
            $appointments = $query->paginate($perPage);
            $data = $appointments->map(function($a) {
                // Собираем все услуги (основная + дочерние)
                $allServices = collect([$a->service->name]);
                $totalPrice = $a->price;
                
                if ($a->childAppointments && $a->childAppointments->count() > 0) {
                    foreach ($a->childAppointments as $child) {
                        $allServices->push($child->service->name);
                        $totalPrice += $child->price;
                    }
                }
                
                // Добавляем стоимость товаров
                $salesTotal = $a->sales->sum('total_amount');
                $totalPrice += $salesTotal;
                
                $servicesText = $allServices->join(' + ');
                
                return [
                    'id' => $a->id,
                    'parent_appointment_id' => $a->parent_appointment_id,
                    'date' => $a->date->format('d.m.Y'),
                    'date_formatted' => $a->date->format('d.m.Y'),
                    'time' => $a->time,
                    'client' => $a->client ? [
                        'id' => $a->client->id,
                        'name' => $a->client->name,
                        'instagram' => $a->client->instagram
                    ] : null,
                    'service' => [
                        'id' => $a->service->id,
                        'name' => $servicesText, // Объединенные услуги
                        'original_name' => $a->service->name // Оригинальное название основной услуги
                    ],
                    'user' => $a->user ? ['name' => $a->user->name] : null,
                    'status' => $a->status,
                    'price' => $totalPrice, // Общая цена всех услуг и товаров
                    'has_children' => $a->childAppointments->count() > 0,
                    'children_count' => $a->childAppointments->count()
                ];
            });
            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Переместить запись на другую дату (drag-and-drop в календаре)
     */
    public function move(Request $request, Appointment $appointment)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        
        // Если пользователь не админ, проверяем что это его запись
        if ($currentUser->role !== 'admin' && $appointment->user_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        try {
            DB::beginTransaction();
            $appointment->update([
                'date' => $request->date,
                'time' => $request->time
            ]);
            // Обновить дату во всех связанных продажах
            foreach ($appointment->sales as $sale) {
                $sale->update(['date' => $request->date]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Дата и время записи и продаж успешно обновлены']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ошибка при переносе: ' . $e->getMessage()], 500);
        }
    }

    // --- Аналитика по сотрудникам ---

    /**
     * Топ-5 сотрудников по количеству процедур
     */
    public function getEmployeesProceduresCount(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed');
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        // Применяем фильтры по дате
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $employeesData = $query->with('user:id,name')
            ->get()
            ->groupBy('user_id')
            ->map(function($appointments, $userId) {
                $user = $appointments->first()->user;
                return [
                    'name' => $user ? $user->name : '—',
                    'count' => $appointments->count()
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return response()->json([
            'labels' => $employeesData->pluck('name')->toArray(),
            'data' => $employeesData->pluck('count')->toArray()
        ]);
    }

    /**
     * Структура процедур по сотрудникам (доли)
     */
    public function getEmployeesProceduresStructure(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed');
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $employeesData = $query->with('user:id,name')
            ->get()
            ->groupBy('user_id')
            ->map(function($appointments, $userId) {
                $user = $appointments->first()->user;
                return [
                    'name' => $user ? $user->name : '—',
                    'count' => $appointments->count()
                ];
            })
            ->values();

        return response()->json([
            'labels' => $employeesData->pluck('name')->toArray(),
            'data' => $employeesData->pluck('count')->toArray()
        ]);
    }

    /**
     * Динамика процедур по сотрудникам (по месяцам)
     */
    public function getEmployeesProceduresDynamics(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed');
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $appointments = $query->with('user:id,name')->get();

        // Группируем по месяцам
        $byMonth = $appointments->groupBy(function($appointment) {
            return Carbon::parse($appointment->date)->format('m.Y');
        });

        $allMonths = $byMonth->keys()->sort()->values()->all();
        
        // Получаем всех сотрудников
        $employees = $appointments->groupBy('user_id')->map(function($appointments, $userId) {
            $user = $appointments->first()->user;
            return $user ? $user->name : '—';
        })->values()->all();

        $datasets = [];
        foreach ($employees as $employeeName) {
            $datasets[$employeeName] = [];
            foreach ($allMonths as $month) {
                $count = $byMonth[$month]->filter(function($appointment) use ($employeeName) {
                    $user = $appointment->user;
                    return $user && $user->name === $employeeName;
                })->count();
                $datasets[$employeeName][] = $count;
            }
        }

        return response()->json([
            'labels' => $allMonths,
            'datasets' => $datasets
        ]);
    }

    /**
     * Среднее время процедуры по сотрудникам
     */
    public function getEmployeesAverageTime(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed')
            ->whereNotNull('duration');
        
        // Если пользователь не админ, показываем только его записи
        $currentUser = auth()->user();
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $employeesData = $query->with('user:id,name')
            ->get()
            ->groupBy('user_id')
            ->map(function($appointments, $userId) {
                $user = $appointments->first()->user;
                return [
                    'name' => $user ? $user->name : '—',
                    'avg_duration' => round($appointments->avg('duration'), 1)
                ];
            })
            ->values();

        return response()->json([
            'labels' => $employeesData->pluck('name')->toArray(),
            'data' => $employeesData->pluck('avg_duration')->toArray()
        ]);
    }

    /**
     * Топ-5 сотрудников по выручке от процедур
     */
    public function getEmployeesRevenue(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed');
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $employeesData = $query->with('user:id,name')
            ->get()
            ->groupBy('user_id')
            ->map(function($appointments, $userId) {
                $user = $appointments->first()->user;
                $totalRevenue = $appointments->sum('price') ?? 0;
                return [
                    'name' => $user ? $user->name : '—',
                    'revenue' => $totalRevenue
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        return response()->json([
            'labels' => $employeesData->pluck('name')->toArray(),
            'data' => $employeesData->pluck('revenue')->toArray()
        ]);
    }

    /**
     * Средний чек по сотрудникам
     */
    public function getEmployeesAverageCheck(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $currentUser = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period', 'week');

        $query = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed')
            ->whereNotNull('price')
            ->where('price', '>', 0);
        
        // Если пользователь не админ, показываем только его записи
        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $this->applyPeriodFilter($query, $period);
        }

        $employeesData = $query->with('user:id,name')
            ->get()
            ->groupBy('user_id')
            ->map(function($appointments, $userId) {
                $user = $appointments->first()->user;
                return [
                    'name' => $user ? $user->name : '—',
                    'avg_check' => round($appointments->avg('price'), 2)
                ];
            })
            ->values();

        return response()->json([
            'labels' => $employeesData->pluck('name')->toArray(),
            'data' => $employeesData->pluck('avg_check')->toArray()
        ]);
    }

    /**
     * Вспомогательный метод для применения фильтров по периодам
     */
    private function applyPeriodFilter($query, $period)
    {
        $end = Carbon::now();
        
        switch ($period) {
            case 'week':
                $start = $end->copy()->startOfWeek();
                break;
            case '2weeks':
                $start = $end->copy()->subWeeks(2)->startOfWeek();
                break;
            case 'month':
                $start = $end->copy()->startOfMonth();
                break;
            case 'half_year':
                $start = $end->copy()->subMonths(6)->startOfMonth();
                break;
            case 'year':
                $start = $end->copy()->subYear()->startOfMonth();
                break;
            default:
                $start = $end->copy()->startOfWeek();
        }
        
        $query->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
    }
}
