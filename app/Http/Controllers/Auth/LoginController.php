<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    }

    public function showLoginForm()
    {
        return view('front.login');
    }

    protected function redirectTo()
    {
        $role = Auth::user()->roles()->first()->name;
        switch ($role) {
            case 'agent':
                return route('agent.index');
                break;
            case 'admin':
                return route('admin.index');
            case 'reporter':
                return route('report.index');
                break;
            default:
                return url('/');
                break;
        }
    }

    public function authenticated(Request $request, $user)
    {
        $user->user_logins()->create([
            'session_id' => session()->getId(),
            'login_time' => Carbon::now()
        ]);
    }
}
