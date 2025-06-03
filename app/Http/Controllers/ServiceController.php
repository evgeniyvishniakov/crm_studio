<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.list', compact('services'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name',
                'price' => 'nullable|numeric|min:0'
            ], [
                'name.required' => 'Поле "Название" обязательно для заполнения.',
                'name.unique' => 'Услуга с таким названием уже существует.',
                'price.numeric' => 'Цена должна быть числом.',
                'price.min' => 'Цена не может быть отрицательной.'
            ]);

            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'service' => $service
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

    public function destroy(Service $service)
    {
        try {
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Услуга успешно удалена'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении услуги'
            ], 500);
        }
    }

    public function edit(Service $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name,'.$service->id,
                'price' => 'nullable|numeric|min:0'
            ], [
                'name.required' => 'Поле "Название" обязательно для заполнения.',
                'name.unique' => 'Услуга с таким названием уже существует.',
                'price.numeric' => 'Цена должна быть числом.',
                'price.min' => 'Цена не может быть отрицательной.'
            ]);

            $service->update($validated);

            return response()->json([
                'success' => true,
                'service' => $service
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении услуги'
            ], 500);
        }
    }
}
