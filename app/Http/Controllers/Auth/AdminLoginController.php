<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\PanelAdmin;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Проверяем, что пользователь существует в таблице panel_admins
        $panelAdmin = PanelAdmin::where('email', $credentials['email'])->first();
        
        if (!$panelAdmin || !Hash::check($credentials['password'], $panelAdmin->password)) {
            return back()->withErrors([
                'email' => 'Неверные учетные данные.',
            ])->withInput($request->only('email'));
        }

        // Все пользователи в таблице panel_admins уже являются админами панели
        // Дополнительная проверка не нужна

        // Входим в систему через guard 'panel'
        Auth::guard('panel')->login($panelAdmin);
        
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('panel')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
