<?php

namespace App\Http\Controllers;

use App\Cdr;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function test()
    {
        /*$start = Carbon::parse("2020-04-08");
        $end = Carbon::parse("2020-04-08");
        $cdrs = Cdr::query()->whereBetween(DB::raw('date(start)'), [$start, $end])->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile']);
        return dd($cdrs);*/

        /** @var User $user */
        $user = Auth::user();
        $res = $user->roles()->get(['name'])->contains('name', null, 'agent');
        return dd($user->endpoints()->first()->id);
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
