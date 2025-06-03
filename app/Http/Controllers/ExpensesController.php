<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();
        return view('expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'comment' => 'required|string',
                'amount' => 'required|numeric|min:0'
            ]);

            $expense = Expense::create($validated);

            return response()->json([
                'success' => true,
                'expense' => $expense,
                'message' => 'Расход успешно добавлен'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении расхода: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Expense $expense)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'comment' => 'required|string',
                'amount' => 'required|numeric|min:0'
            ]);

            $expense->update($validated);

            return response()->json([
                'success' => true,
                'expense' => $expense,
                'message' => 'Расход успешно обновлен'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении расхода: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Расход успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении расхода: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Expense $expense)
    {
        try {
            return response()->json([
                'success' => true,
                'expense' => $expense
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }
} 