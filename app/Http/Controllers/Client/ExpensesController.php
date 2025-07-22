<?php

namespace App\Http\Controllers\Client;

use App\Models\Clients\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Expense::where('project_id', $currentProjectId)->orderBy('date', 'desc');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%$search%");
            });
        }

        if ($request->ajax()) {
            $expenses = $query->paginate(11);
            $categories = config('expenses.categories', [
                'Аренда и коммуналка',
                'Зарплата', 
                'Материалы',
                'Реклама',
                'Налоги',
                'Прочее'
            ]);
            return response()->json([
                'data' => $expenses->items(),
                'meta' => [
                    'current_page' => $expenses->currentPage(),
                    'last_page' => $expenses->lastPage(),
                    'per_page' => $expenses->perPage(),
                    'total' => $expenses->total(),
                ],
                'categories' => $categories,
            ]);
        }

        $expenses = $query->paginate(11);
        $categories = config('expenses.categories', [
            'Аренда и коммуналка',
            'Зарплата', 
            'Материалы',
            'Реклама',
            'Налоги',
            'Прочее'
        ]);
        
        return view('client.expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'comment' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|in:' . implode(',', config('expenses.categories'))
            ]);

            $expense = Expense::create($validated + ['project_id' => $currentProjectId]);

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
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'comment' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|in:' . implode(',', config('expenses.categories'))
            ]);

            if ($expense->project_id !== $currentProjectId) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к расходу'], 403);
            }
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
        $currentProjectId = auth()->user()->project_id;
        try {
            if ($expense->project_id !== $currentProjectId) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к расходу'], 403);
            }
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
        $currentProjectId = auth()->user()->project_id;
        try {
            if ($expense->project_id !== $currentProjectId) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к расходу'], 403);
            }
            return response()->json([
                'success' => true,
                'expense' => [
                    'id' => $expense->id,
                    'date' => $expense->date ? $expense->date->format('Y-m-d') : null,
                    'comment' => $expense->comment,
                    'amount' => $expense->amount,
                    'category' => $expense->category,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }
} 