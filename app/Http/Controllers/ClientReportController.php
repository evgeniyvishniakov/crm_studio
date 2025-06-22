<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientReportController extends Controller
{
    /**
     * Отображает страницу отчетов по клиентам.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Пока мы просто возвращаем представление.
        // Позже здесь будет логика для сбора данных для отчетов.
        return view('reports.clients');
    }
} 