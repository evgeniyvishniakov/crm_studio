<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    public function changeEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email|unique:admin_users,email',
            'current_password' => 'required',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->input('current_password'), $user->password)) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['current_password' => 'Неверный текущий пароль.']], 422);
            }
            return back()->withErrors(['current_password' => 'Неверный текущий пароль.']);
        }

        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);
        $user->new_email = $request->input('new_email');
        $user->email_change_token = $token;
        $user->email_change_token_expires_at = $expiresAt;
        $user->save();

        $this->sendEmailChangeConfirmation($user);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Проверьте новый email для подтверждения.']);
        }
        return back()->with('success', 'Проверьте новый email для подтверждения.');
    }

    private function sendEmailChangeConfirmation($user)
    {
        $confirmUrl = url('/email/change/confirm?token=' . $user->email_change_token);
        $to = $user->new_email;
        $subject = 'Подтвердите смену email в Trimora CRM';
        $body = "Здравствуйте!\n\nВы запросили смену email для своей учётной записи.\nПожалуйста, перейдите по ссылке для подтверждения: $confirmUrl\n\nЕсли вы не запрашивали смену email, просто проигнорируйте это письмо.";
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    public function enable2fa(Request $request)
    {
        // Здесь будет логика включения 2FA (например, генерация секретного ключа, показ QR-кода и т.д.)
        return back()->with('success', '2FA включена (заглушка)');
    }

    public function disable2fa(Request $request)
    {
        // Здесь будет логика отключения 2FA
        return back()->with('success', '2FA отключена (заглушка)');
    }

    public function confirmEmailChange(Request $request)
    {
        $token = $request->query('token');
        $user = \App\Models\Admin\User::where('email_change_token', $token)
            ->where('email_change_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Ссылка недействительна или устарела.']);
        }

        // Меняем email
        $user->email = $user->new_email;
        $user->new_email = null;
        $user->email_change_token = null;
        $user->email_change_token_expires_at = null;
        $user->save();

        // Синхронизируем email в проекте
        $project = \App\Models\Admin\Project::find($user->project_id);
        if ($project) {
            $project->email = $user->email;
            $project->save();
        }

        // Можно авторизовать пользователя или просто показать сообщение
        return redirect('/login')->with('success', 'Email успешно изменён!');
    }
}
