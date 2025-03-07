<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        } else if ($status === Password::INVALID_USER) {
            return response()->json(['error' => 'Invalid user.'], 400);
        } else {
            return response()->json(['error' => 'Unable to send reset link.'], 400);
        }
    }
    public function RedirectShowResetPasswordForm(Request $request, $token): Application|RedirectResponse|Redirector|JsonResponse
    {
        // دریافت ایمیل از کوئری استرینگ
        $email = $request->query('email');

        // اطمینان از اینکه ایمیل و توکن موجود است
        // بررسی صحت ایمیل و توکن
        if (!$email || !$token) {
            return response()->json(['error' => 'ایمیل یا توکن نامعتبر است.'], 400);
        }
// ایجاد پارامترهای کوئری
        $query_params = [
            'email' => $email,
            'token' => $token
        ];

        // ایجاد URL برای ریدایرکت به فرانت‌اند
        $url = 'https://nemoonehshow.ir/reset-password';
        $redirect_url = $url . '?' . http_build_query($query_params);

        // ریدایرکت به فرانت‌اند
        return Redirect::to($redirect_url);
    }


    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
