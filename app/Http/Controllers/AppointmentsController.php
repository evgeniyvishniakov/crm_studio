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
        $products = Product::with('warehouse')->get();

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

    public function update(Request $request, Appointment $appointment)
    {
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'client_id' => 'required|exists:clients,id',
                'date' => 'required|date',
                'time' => 'required',
                'price' => 'required|numeric|min:0',
                'products' => 'sometimes|array',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.price' => 'required|numeric|min:0',
                'products.*.wholesale_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            try {
                // Update appointment
                $appointment->update([
                    'service_id' => $validated['service_id'],
                    'client_id' => $validated['client_id'],
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                    'price' => $validated['price']
                ]);

                // Delete old sales and return products to warehouse
                foreach ($appointment->sales as $sale) {
                    foreach ($sale->items as $item) {
                        if ($item->product && $item->product->warehouse) {
                            $item->product->warehouse->increment('quantity', $item->quantity);
                        }
                    }
                    $sale->items()->delete();
                    $sale->delete();
                }

                // Create new sales with products if they exist
                if (!empty($validated['products'])) {
                    $sale = Sale::create([
                        'appointment_id' => $appointment->id,
                        'client_id' => $appointment->client_id,
                        'date' => $appointment->date,
                        'total_amount' => 0
                    ]);

                    $totalAmount = 0;
                    foreach ($validated['products'] as $product) {
                        $productModel = Product::with('warehouse')->findOrFail($product['product_id']);

                        // Check warehouse availability
                        if (!$productModel->warehouse || $productModel->warehouse->quantity < $product['quantity']) {
                            throw new \Exception("Недостаточно товара на складе: {$productModel->name}");
                        }

                        // Create sale item
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'retail_price' => $product['price'],
                            'wholesale_price' => $product['wholesale_price'],
                            'total' => $product['quantity'] * $product['price']
                        ]);

                        // Update total amount
                        $totalAmount += $product['quantity'] * $product['price'];

                        // Decrease warehouse quantity
                        $productModel->warehouse->decrement('quantity', $product['quantity']);
                    }

                    // Update sale total amount
                    $sale->update(['total_amount' => $totalAmount]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Запись успешно обновлена',
                    'appointment' => $appointment->fresh(['client', 'service', 'sales.items.product.warehouse'])
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
                'products' => Product::with('warehouse')->get()
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
                'wholesale_price' => 'required|numeric|min:0'
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
                    'wholesale_price' => $validated['wholesale_price'],
                    'total' => $validated['quantity'] * $validated['price']
                ]);

                // Обновляем общую сумму продажи
                $sale->increment('total_amount', $saleItem->total);

                // Уменьшаем количество на складе
                $product->warehouse->decrement('quantity', $validated['quantity']);

                return response()->json([
                    'success' => true,
                    'message' => 'Товар успешно добавлен',
                    'appointment' => $appointment->fresh(['sales.items.product.warehouse'])
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

}
