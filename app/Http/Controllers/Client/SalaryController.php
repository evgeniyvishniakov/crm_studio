<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Clients\SalarySetting;
use App\Models\Clients\SalaryCalculation;
use App\Models\Clients\SalaryPayment;
use App\Models\Admin\User;
use App\Models\Clients\Appointment;
use App\Models\Clients\Sale;
use Carbon\Carbon;
use App\Helpers\CurrencyHelper;

class SalaryController extends Controller
{
    /**
     * Показать главную страницу модуля зарплаты
     */
    public function index()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;

        // Получаем сотрудников проекта
        $employees = User::where('project_id', $project->id)->get();

        // Получаем настройки зарплаты
        $salarySettings = SalarySetting::where('project_id', $project->id)
            ->with('user')
            ->get();

        // Получаем расчеты зарплаты
        $salaryCalculations = SalaryCalculation::where('project_id', $project->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Получаем выплаты
        $salaryPayments = SalaryPayment::where('project_id', $project->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Статистика
        $stats = [
            'total_employees' => $employees->count(),
            'employees_with_salary' => $salarySettings->count(),
            'calculations_this_month' => $salaryCalculations->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'payments_this_month' => $salaryPayments->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'total_payments_this_month' => $salaryPayments->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('amount'),
        ];

        // Последние расчеты
        $recentCalculations = $salaryCalculations->take(5);

        // Последние выплаты
        $recentPayments = $salaryPayments->take(5);

        // Статистика по месяцам
        $monthlyStats = SalaryCalculation::where('project_id', $project->id)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as calculations_count, SUM(total_salary) as total_salary, AVG(total_salary) as avg_salary')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Топ сотрудников
        $topEmployees = SalaryCalculation::where('project_id', $project->id)
            ->selectRaw('user_id, SUM(total_salary) as total_earned, COUNT(*) as calculations_count')
            ->groupBy('user_id')
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->with('user')
            ->get();

        // Получаем данные о валюте
        $currencyData = CurrencyHelper::getCurrencyData();

        return view('client.salary.index', compact(
            'employees',
            'salarySettings',
            'salaryCalculations',
            'salaryPayments',
            'stats',
            'recentCalculations',
            'recentPayments',
            'monthlyStats',
            'topEmployees',
            'currencyData'
        ));
    }

    /**
     * Сохранить настройки зарплаты
     */
    public function storeSetting(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:admin_users,id',
            'salary_type' => 'required|in:fixed,percentage,mixed',
            'fixed_salary' => 'nullable|numeric|min:0',
            'service_percentage' => 'nullable|numeric|min:0|max:100',
            'sales_percentage' => 'nullable|numeric|min:0|max:100',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
        ]);

        $project = Auth::guard('client')->user()->project;

        // Проверяем, что настройки для этого сотрудника еще не существуют
        $existingSetting = SalarySetting::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingSetting) {
            return response()->json([
                'success' => false,
                'message' => 'Настройки зарплаты для этого сотрудника уже существуют'
            ]);
        }

        $setting = SalarySetting::create([
            'project_id' => $project->id,
            'user_id' => $request->user_id,
            'salary_type' => $request->salary_type,
            'fixed_salary' => $request->fixed_salary,
            'service_percentage' => $request->service_percentage,
            'sales_percentage' => $request->sales_percentage,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Настройки зарплаты созданы успешно'
        ]);
    }

    /**
     * Получить данные настроек для редактирования
     */
    public function editSetting($id)
    {
        $setting = SalarySetting::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Обновить настройки зарплаты
     */
    public function updateSetting(Request $request, $id)
    {
        $request->validate([
            'salary_type' => 'required|in:fixed,percentage,mixed',
            'fixed_salary' => 'nullable|numeric|min:0',
            'service_percentage' => 'nullable|numeric|min:0|max:100',
            'sales_percentage' => 'nullable|numeric|min:0|max:100',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
        ]);

        $setting = SalarySetting::findOrFail($id);
        
        $setting->update([
            'salary_type' => $request->salary_type,
            'fixed_salary' => $request->fixed_salary,
            'service_percentage' => $request->service_percentage,
            'sales_percentage' => $request->sales_percentage,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Настройки зарплаты обновлены успешно'
        ]);
    }

    /**
     * Удалить настройки зарплаты
     */
    public function destroySetting($id)
    {
        $setting = SalarySetting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Настройки зарплаты удалены успешно'
        ]);
    }

    /**
     * Создать расчет зарплаты
     */
    public function createCalculation()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        
        $employees = User::where('project_id', $project->id)->get();
        $salarySettings = SalarySetting::where('project_id', $project->id)->get();

        return view('client.salary.calculations.create', compact('employees', 'salarySettings'));
    }

    /**
     * Сохранить расчет зарплаты
     */
    public function storeCalculation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:admin_users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'bonuses' => 'nullable|numeric|min:0',
            'penalties' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $project = Auth::guard('client')->user()->project;
        
        // Получаем настройки зарплаты
        $salarySetting = SalarySetting::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$salarySetting) {
            return response()->json([
                'success' => false,
                'message' => 'Настройки зарплаты для этого сотрудника не найдены'
            ]);
        }

        // Получаем услуги за период
        $services = Appointment::where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->whereBetween('date', [$request->period_start, $request->period_end])
            ->where('status', 'completed')
            ->get();

        // Получаем продажи за период
        $sales = Sale::where('project_id', $project->id)
            ->where('employee_id', $request->user_id)
            ->whereBetween('date', [$request->period_start, $request->period_end])
            ->get();

        // Рассчитываем зарплату
        $calculation = $salarySetting->calculateSalary(
            $services,
            $sales,
            $request->period_start,
            $request->period_end,
            $request->bonuses ?? 0,
            $request->penalties ?? 0,
            $request->notes
        );

        // Загружаем связанные данные для отображения в таблице
        $calculation->load('user');
        
        return response()->json([
            'success' => true,
            'message' => 'Расчет зарплаты создан успешно',
            'calculation' => [
                'id' => $calculation->id,
                'user_name' => $calculation->user->name,
                'period_start' => $calculation->period_start,
                'period_end' => $calculation->period_end,
                'total_salary' => $calculation->total_salary,
                'services_count' => $calculation->services_count,
                'services_amount' => $calculation->services_amount,
                'sales_count' => $calculation->sales_count,
                'sales_amount' => $calculation->sales_amount,
                'status' => $calculation->status
            ]
        ]);
    }

    /**
     * Показать детали расчета
     */
    public function showCalculation($id)
    {
        $calculation = SalaryCalculation::with(['user', 'project', 'payments'])->findOrFail($id);
        
        return view('client.salary.calculations.show', compact('calculation'));
    }

    /**
     * Утвердить расчет
     */
    public function approveCalculation($id)
    {
        $calculation = SalaryCalculation::findOrFail($id);
        $calculation->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Расчет утвержден успешно'
        ]);
    }

    /**
     * Создать выплату
     */
    public function createPayment()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        
        $employees = User::where('project_id', $project->id)->get();
        $calculations = SalaryCalculation::where('project_id', $project->id)
            ->where('status', 'approved')
            ->with('user')
            ->get();

        return view('client.salary.payments.create', compact('employees', 'calculations'));
    }

    /**
     * Сохранить выплату
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:admin_users,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,card',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $project = Auth::guard('client')->user()->project;
        $user = Auth::guard('client')->user();

        $payment = SalaryPayment::create([
            'project_id' => $project->id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        // Загружаем связанные данные для отображения в таблице
        $payment->load('user');
        
        return response()->json([
            'success' => true,
            'message' => 'Выплата создана успешно',
            'payment' => [
                'id' => $payment->id,
                'user_name' => $payment->user->name,
                'payment_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status
            ]
        ]);
    }

    /**
     * Показать детали выплаты
     */
    public function showPayment($id)
    {
        $payment = SalaryPayment::with(['user', 'project', 'calculation', 'createdBy', 'approvedBy'])->findOrFail($id);
        
        return view('client.salary.payments.show', compact('payment'));
    }

    /**
     * Получить детали выплаты для модального окна
     */
    public function getPaymentDetails($id)
    {
        $payment = SalaryPayment::with(['user', 'createdBy', 'approvedBy'])->findOrFail($id);
        $user = Auth::guard('client')->user();
        
        // Проверяем, что выплата принадлежит проекту пользователя
        if ($payment->project_id !== $user->project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'user_name' => $payment->user->name,
                'payment_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
                'status' => $payment->status,
                'notes' => $payment->notes,
                'created_at' => $payment->created_at,
                'approved_by' => $payment->approved_by,
                'approved_at' => $payment->approved_at,
                'approved_by_name' => $payment->approvedBy ? $payment->approvedBy->name : null,
            ]
        ]);
    }

    /**
     * Утвердить выплату
     */
    public function approvePayment($id)
    {
        $payment = SalaryPayment::findOrFail($id);
        $user = Auth::guard('client')->user();
        
        $payment->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Выплата подтверждена успешно'
        ]);
    }

    /**
     * Получить расчеты зарплаты для конкретного сотрудника
     */
    public function getCalculationsByUser($userId)
    {
        $project = Auth::guard('client')->user()->project;
        
        $calculations = SalaryCalculation::where('project_id', $project->id)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'period_start', 'period_end', 'total_salary', 'status']);

        return response()->json([
            'success' => true,
            'calculations' => $calculations
        ]);
    }

    /**
     * Удалить расчет зарплаты
     */
    public function destroyCalculation($id)
    {
        $calculation = SalaryCalculation::findOrFail($id);
        $user = Auth::guard('client')->user();
        
        // Проверяем, что расчет принадлежит проекту пользователя
        if ($calculation->project_id !== $user->project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }
        
        // Проверяем, можно ли удалить
        if (!$calculation->canDelete()) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно удалить этот расчет'
            ], 400);
        }
        
        $calculation->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Расчет зарплаты удален успешно'
        ]);
    }

    /**
     * Удалить выплату зарплаты
     */
    public function destroyPayment($id)
    {
        $payment = SalaryPayment::findOrFail($id);
        $user = Auth::guard('client')->user();
        
        // Проверяем, что выплата принадлежит проекту пользователя
        if ($payment->project_id !== $user->project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }
        
        // Проверяем, можно ли удалить
        if (!$payment->canDelete()) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно удалить эту выплату'
            ], 400);
        }
        
        $payment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Выплата зарплаты удалена успешно'
        ]);
    }

    /**
     * Получить детали расчета зарплаты для просмотра
     */
    public function getCalculationDetails($id)
    {
        $project = Auth::guard('client')->user()->project;
        
        $calculation = SalaryCalculation::with(['user', 'project'])
            ->where('project_id', $project->id)
            ->findOrFail($id);

        // Получаем настройки зарплаты для этого сотрудника
        $salarySetting = SalarySetting::where('project_id', $project->id)
            ->where('user_id', $calculation->user_id)
            ->first();

        return response()->json([
            'success' => true,
            'calculation' => [
                'id' => $calculation->id,
                'user_name' => $calculation->user->name,
                'period_start' => $calculation->period_start,
                'period_end' => $calculation->period_end,
                'services_count' => $calculation->services_count,
                'services_amount' => $calculation->services_amount,
                'sales_count' => $calculation->sales_count,
                'sales_amount' => $calculation->sales_amount,
                'fixed_salary' => $calculation->fixed_salary,
                'percentage_salary' => $calculation->percentage_salary,
                'bonuses' => $calculation->bonuses,
                'penalties' => $calculation->penalties,
                'total_salary' => $calculation->total_salary,
                'status' => $calculation->status,
                'status_text' => $calculation->getStatusTextAttribute(),
                'status_color' => $calculation->getStatusColorAttribute(),
                'notes' => $calculation->notes,
                'created_at' => $calculation->created_at,
                // Добавляем проценты из настроек зарплаты
                'service_percentage' => $salarySetting ? $salarySetting->service_percentage : null,
                'sales_percentage' => $salarySetting ? $salarySetting->sales_percentage : null,
            ]
        ]);
    }
}
