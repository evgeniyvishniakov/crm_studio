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
        $viewType = $request->get('view', 'list'); // list или calendar

        $appointments = Appointment::with(['client', 'service', 'user'])
            ->where('project_id', $currentProjectId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(11);

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
                'user_id' => 'nullable|exists:admin_users,id',
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
                $appointment = Appointment::with(['sales.items'])->findOrFail($id);
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
                    }
                }

                DB::commit();

                $appointment = $appointment->fresh(['client', 'service', 'sales.items.product.warehouse', 'user']);
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
        try {
            $appointment = Appointment::with('sales.items')->where('project_id', $currentProjectId)->find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => true, // Все равно считаем успехом, так как запись уже удалена
                    'message' => 'Запись не найдена (возможно, уже удалена)'
                ]);
            }

            DB::transaction(function () use ($appointment) {
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
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        try {
            $appointmentData = $appointment->load(['client', 'service', 'user']);
            
            // Определяем длительность: сначала из записи, если нет - из услуги
            $duration = $appointment->duration ?? $appointment->service->duration ?? 0;

            // Добавляем вычисление часов и минут для формы
            $appointmentData->duration_hours = floor($duration / 60);
            $appointmentData->duration_minutes = $duration % 60;

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
        try {
            $appointment = Appointment::with(['client', 'service', 'sales.items.product', 'user'])
                ->where('project_id', $currentProjectId)
                ->findOrFail($id);

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

            return response()->json([
                'success' => true,
                'appointment' => $appointment,
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
                $appointment = Appointment::where('project_id', $currentProjectId)->findOrFail($appointmentId);
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
                $sales = Sale::with(['items.product'])
                    ->where('client_id', $appointment->client_id)
                    ->whereDate('date', $appointment->date)
                    ->get();

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
        try {
            return DB::transaction(function () use ($appointmentId, $saleId, $currentProjectId) {
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
        if ($appointment->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к записи'], 403);
        }
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'price' => 'required|numeric|min:0',
            ]);

            $newAppointment = Appointment::create([
                'client_id' => $appointment->client_id,
                'service_id' => $validated['service_id'],
                'date' => $appointment->date,
                'time' => $appointment->time,
                'price' => $validated['price'],
                'notes' => 'Добавлено через просмотр записи',
                'project_id' => $currentProjectId
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
        if ($appointment->project_id !== $currentProjectId) {
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
        $currentProjectId = auth()->user()->project_id;
        try {
            $appointments = Appointment::with(['client', 'service'])
                ->where('project_id', $currentProjectId)
                ->when($request->start, function($query) use ($request) {
                    return $query->whereDate('date', '>=', Carbon::parse($request->start));
                })
                ->when($request->end, function($query) use ($request) {
                    return $query->whereDate('date', '<=', Carbon::parse($request->end));
                })
                ->get();

            $events = $appointments->map(function($appointment) {
                try {
                    // Правильное форматирование даты и времени
                    $date = Carbon::parse($appointment->date)->format('Y-m-d');
                    $startDateTime = Carbon::parse($date . ' ' . $appointment->time);
                    $duration = $appointment->duration ?? $appointment->service->duration ?? 60;
                    $endDateTime = $startDateTime->copy()->addMinutes($duration);

                    $event = [
                        'id' => $appointment->id,
                        'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                        'start' => $startDateTime->format('Y-m-d\TH:i:s'),
                        'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                        'date' => $date,
                        'classNames' => ['status-' . $appointment->status],
                        'extendedProps' => [
                            'client' => $appointment->client->name,
                            'service' => $appointment->service->name,
                            'price' => $appointment->price,
                            'notes' => $appointment->notes,
                            'status' => $appointment->status,
                            'time' => $appointment->time,
                            'duration' => $duration
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
        $appointments = Appointment::with(['client', 'service'])->where('project_id', $currentProjectId)->get();

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
        try {
            $appointment = Appointment::with(['client', 'service', 'sales.items.product.warehouse'])
                ->where('project_id', $currentProjectId)
                ->findOrFail($id);

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
        if ($appointment->project_id !== $currentProjectId) {
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

        $statuses = Appointment::where('project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();
        
        $statusLabels = [
            'completed' => 'Выполнено',
            'cancelled' => 'Отменено',
            'pending' => 'В ожидании',
            'rescheduled' => 'Перенесено',
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
                'labels' => ['Нет данных'],
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

        $servicePopularity = Appointment::where('appointments.project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate])
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(appointments.id) as count'))
            ->groupBy('services.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'name');

        if ($servicePopularity->isEmpty()) {
            return response()->json([
                'labels' => ['Нет данных'],
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
                $rawData = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange)
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
                $rawData = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange)
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
                $data = Appointment::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('date', $periodRange)
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
        $checks = Appointment::where('project_id', $currentProjectId)
            ->whereBetween('date', [$startDate, $endDate])
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
            $perPage = (int)($request->get('per_page', 11));
            $search = $request->get('search');
            $query = Appointment::with(['client', 'service', 'user'])
                ->where('project_id', $currentProjectId);
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
                return [
                    'id' => $a->id,
                    'date' => $a->date,
                    'time' => $a->time,
                    'client' => $a->client ? [
                        'id' => $a->client->id,
                        'name' => $a->client->name,
                        'instagram' => $a->client->instagram
                    ] : null,
                    'service' => $a->service ? [
                        'id' => $a->service->id,
                        'name' => $a->service->name
                    ] : null,
                    'user' => $a->user ? ['name' => $a->user->name] : null,
                    'status' => $a->status,
                    'price' => $a->price,
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
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);
        if ($appointment->project_id !== $currentProjectId) {
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
}
