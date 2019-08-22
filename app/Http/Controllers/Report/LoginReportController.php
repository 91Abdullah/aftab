<?php

namespace App\Http\Controllers\Report;

use App\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LoginReportController extends Controller
{
    public function index()
    {
        return view('report.login.index');
    }

    public function getReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $records = UserLogin::whereBetween(DB::raw('date(login_time)'), [$start, $end]);

        return DataTables::of($records)
            ->editColumn('user_id', function (UserLogin $userLogin) {
                return $userLogin->user->name;
            })
            ->toJson();
    }
}
