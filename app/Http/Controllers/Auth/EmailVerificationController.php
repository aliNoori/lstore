<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class EmailVerificationController extends Controller
{
    /**
     * نمایش فرم تأیید ایمیل.
     *
     * @return Factory|View|Application
     */
    public function show(): Factory|View|Application
    {
        return view('auth.verify-email');
    }
}
