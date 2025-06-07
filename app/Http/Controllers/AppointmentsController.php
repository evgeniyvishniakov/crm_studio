<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Service;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $viewType = $request->get('view', 'list'); // list или calendar

        $appointments = Appointment::with(['client', 'service'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        $clients = Client::all()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'instagram' => $client->instagram,
                'email' => $client->email,
                'phone' => $client->phone,
            ];
        });

        $services = Service::all();
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

        return view('appointments.list', compact(
            'appointments',
            'clients',
            'services',
            'viewType',
            'products'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'service_id' => 'required|exists:services,id',
                'date' => 'required|date',
                'time' => 'required',
                'price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string'
            ]);

            $appointment = Appointment::create($validated);

            return response()->json([
                'success' => true,
                'appointment' => $appointment->load(['client', 'service'])
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
                'sales' => 'array',
                'sales.*.product_id' => 'required|exists:products,id',
                'sales.*.quantity' => 'required|integer|min:1',
                'sales.*.price' => 'required|numeric|min:0',
                'sales.*.purchase_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            try {
                $appointment = Appointment::findOrFail($id);
                
                // Обновляем основные данные записи
                $appointment->update([
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                    'client_id' => $validated['client_id'],
                    'service_id' => $validated['service_id'],
                    'price' => $validated['price']
                ]);

                // Удаляем старые продажи и возвращаем товары на склад
                if ($appointment->sales) {
                    foreach ($appointment->sales as $sale) {
                        foreach ($sale->items as $item) {
                            if ($item->product && $item->product->warehouse) {
                                $item->product->warehouse->increment('quantity', $item->quantity);
                            }
                        }
                        $sale->items()->delete();
                        $sale->delete();
                    }
                }

                // Создаем новую продажу если есть товары
                if (!empty($validated['sales'])) {
                    $sale = Sale::create([
                        'appointment_id' => $appointment->id,
                        'client_id' => $appointment->client_id,
                        'date' => $appointment->date,
                        'total_amount' => 0
                    ]);

                    $totalAmount = 0;
                    foreach ($validated['sales'] as $saleData) {
                        $product = Product::with('warehouse')->findOrFail($saleData['product_id']);

                        // Проверяем наличие на складе
                        if (!$product->warehouse || $product->warehouse->quantity < $saleData['quantity']) {
                            throw new \Exception("Недостаточно товара на складе: {$product->name}");
                        }

                        // Создаем позицию продажи
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $saleData['product_id'],
                            'quantity' => $saleData['quantity'],
                            'retail_price' => $saleData['price'],
                            'wholesale_price' => $saleData['purchase_price'],
                            'total' => $saleData['quantity'] * $saleData['price']
                        ]);

                        // Обновляем общую сумму
                        $totalAmount += $saleData['quantity'] * $saleData['price'];

                        // Уменьшаем количество на складе
                        $product->warehouse->decrement('quantity', $saleData['quantity']);
                    }

                    // Обновляем общую сумму продажи
                    $sale->update(['total_amount' => $totalAmount]);
                }

                DB::commit();

                // Получаем обновленные данные записи со всеми связями
                $appointment = $appointment->fresh(['client', 'service', 'sales.items.product.warehouse']);

                // Получаем обновленный список товаров
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
        try {
            $appointment = Appointment::with('sales.items')->find($id);

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
        try {
            return response()->json([
                'success' => true,
                'appointment' => $appointment->load(['client', 'service']),
                'clients' => Client::all(),
                'services' => Service::all()
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
        try {
            $appointment = Appointment::with(['client', 'service'])->findOrFail($id);

            // Получаем все продажи для этого клиента на эту дату
            $sales = Sale::with(['items.product'])
                ->where('client_id', $appointment->client_id)
                ->whereDate('date', $appointment->date)
                ->get();

            // Получаем все товары из продаж
            $saleItems = [];
            foreach ($sales as $sale) {
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
            \Log::error('Error in view appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addProduct(Request $request, $appointmentId)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'purchase_price' => 'required|numeric|min:0'
            ]);

            return DB::transaction(function () use ($validated, $appointmentId) {
                $appointment = Appointment::findOrFail($appointmentId);
                $product = Product::with('warehouse')->findOrFail($validated['product_id']);

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
                        'total_amount' => 0
                    ]
                );

                // Создаем позицию в продаже
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'retail_price' => $validated['price'],
                    'wholesale_price' => $validated['purchase_price'],
                    'total' => $validated['quantity'] * $validated['price']
                ]);

                // Обновляем общую сумму продажи
                $sale->increment('total_amount', $saleItem->total);

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
        try {
            return DB::transaction(function () use ($appointmentId, $saleId) {
                $sale = Sale::where('appointment_id', $appointmentId)
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
                'notes' => 'Добавлено через просмотр записи'
            ]);

            return response()->json([
                'success' => true,
                'appointment' => $newAppointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении процедуры: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeProduct(Appointment $appointment, Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        // Логика удаления товара из записи
        $appointment->products()->detach($validated['product_id']);

        return response()->json(['success' => true]);
    }

    public function calendarEvents(Request $request)
    {
        try {
            \Log::info('calendarEvents method started');
            \Log::info('Request data:', $request->all());

            $appointments = Appointment::with(['client', 'service'])
                ->when($request->start, function($query) use ($request) {
                    \Log::info('Filtering by start date: ' . $request->start);
                    return $query->whereDate('date', '>=', Carbon::parse($request->start));
                })
                ->when($request->end, function($query) use ($request) {
                    \Log::info('Filtering by end date: ' . $request->end);
                    return $query->whereDate('date', '<=', Carbon::parse($request->end));
                })
                ->get();

            \Log::info('Found appointments count: ' . $appointments->count());

            $events = $appointments->map(function($appointment) {
                try {
                    \Log::info('Processing appointment:', ['id' => $appointment->id, 'date' => $appointment->date, 'time' => $appointment->time]);

                    // Правильное форматирование даты и времени
                    $date = Carbon::parse($appointment->date)->format('Y-m-d');
                    $startDateTime = Carbon::parse($date . ' ' . $appointment->time);
                    $endDateTime = $startDateTime->copy()->addMinutes($appointment->service->duration ?? 60);

                    $event = [
                        'id' => $appointment->id,
                        'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                        'start' => $startDateTime->format('Y-m-d\TH:i:s'),
                        'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                        'backgroundColor' => '#4CAF50',
                        'borderColor' => '#4CAF50',
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'client' => $appointment->client->name,
                            'service' => $appointment->service->name,
                            'price' => $appointment->price,
                            'notes' => $appointment->notes
                        ]
                    ];

                    \Log::info('Created event:', $event);
                    return $event;

                } catch (\Exception $e) {
                    \Log::error('Error processing appointment: ' . $e->getMessage(), [
                        'appointment_id' => $appointment->id,
                        'date' => $appointment->date,
                        'time' => $appointment->time
                    ]);
                    return null;
                }
            })->filter()->values();

            \Log::info('Final events count: ' . $events->count());
            return response()->json($events);

        } catch (\Exception $e) {
            \Log::error('Error in calendarEvents: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ошибка при загрузке событий: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEvents()
    {
        $appointments = Appointment::with(['client', 'service'])->get();

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
        try {
            $appointment = Appointment::with(['client', 'service', 'sales.items.product.warehouse'])
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
        try {
            DB::beginTransaction();

            // Получаем текущие продажи
            $currentSales = $appointment->sales()->with('items')->get();

            // Удаляем старые продажи и возвращаем товары на склад
            foreach ($currentSales as $sale) {
                foreach ($sale->items as $item) {
                    if ($item->product && $item->product->warehouse) {
                        $item->product->warehouse->increment('quantity', $item->quantity);
                    }
                }
                $sale->items()->delete();
                $sale->delete();
            }

            // Создаем новую продажу
            if ($request->has('sales')) {
                $sale = Sale::create([
                    'appointment_id' => $appointment->id,
                    'total_amount' => 0
                ]);

                $totalAmount = 0;

                foreach ($request->sales as $saleData) {
                    $item = $sale->items()->create([
                        'product_id' => $saleData['product_id'],
                        'quantity' => $saleData['quantity'],
                        'price' => $saleData['price'],
                        'purchase_price' => $saleData['purchase_price']
                    ]);

                    // Уменьшаем количество товара на складе
                    if ($item->product && $item->product->warehouse) {
                        $item->product->warehouse->decrement('quantity', $item->quantity);
                    }

                    $totalAmount += $saleData['price'] * $saleData['quantity'];
                }

                $sale->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Продажи успешно обновлены',
                'appointment' => $appointment->fresh(['client', 'service', 'sales.items.product'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении продаж: ' . $e->getMessage()
            ], 500);
        }
    }

}
