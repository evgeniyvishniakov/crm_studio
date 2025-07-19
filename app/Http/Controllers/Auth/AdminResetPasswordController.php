<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    /**
     * Get the post password reset redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admin_users');
    }

    /**
     * Правила валидации для сброса/установки пароля
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-zA-Z]/', // хотя бы одна буква
                'regex:/[0-9]/',    // хотя бы одна цифра
                'confirmed',
            ],
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Получаем запись по email
        $tokenRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Проверяем наличие записи и валидность токена через Hash::check
        if (!$tokenRecord || !\Hash::check($request->token, $tokenRecord->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ссылка для сброса пароля недействительна или истекла.']);
        }

        // Проверяем, не истек ли токен (60 минут)
        $tokenAge = now()->diffInMinutes($tokenRecord->created_at);
        if ($tokenAge > 60) {
            // Удаляем истекший токен
            \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ссылка для сброса пароля истекла.']);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($response == Password::PASSWORD_RESET) {
            // Аутентифицируем пользователя с guard 'client' вместо 'web'
            $user = $this->broker()->getUser($this->credentials($request));
            Auth::guard('client')->login($user);
            
            return redirect($this->redirectPath());
        }

        return $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        // Удаляем токен из базы данных после успешного сброса пароля
        \DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;

        // Получаем запись по email
        $tokenRecord = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // Проверяем наличие записи и валидность токена через Hash::check
        if (!$tokenRecord || !\Hash::check($token, $tokenRecord->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ссылка для сброса пароля недействительна или истекла.']);
        }

        // Проверяем, не истек ли токен (60 минут)
        $tokenAge = now()->diffInMinutes($tokenRecord->created_at);
        if ($tokenAge > 60) {
            // Удаляем истекший токен
            \DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ссылка для сброса пароля истекла.']);
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
} 