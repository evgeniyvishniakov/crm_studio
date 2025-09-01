<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Удалён метод credentials(), теперь используется стандартная аутентификация по email и password

    public function showLoginForm()
    {
        // Если пользователь уже авторизован и это админ — редирект на /panel
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect('/panel');
        }
        // Для админки возвращаем отдельный шаблон
        return view('admin.login');
    }

    public function login(\Illuminate\Http\Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            if ($user->role !== 'admin') {
                auth()->logout();
                return back()->withErrors(['email' => 'Нет доступа к админке.']);
            }
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'Неверный email или пароль.']);
    }
}
