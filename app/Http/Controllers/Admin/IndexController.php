<?php

namespace App\Http\Controllers\Admin;

use App\Cdr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $data = [];
        $data['calls'] = Cdr::query()->whereMonth('start', Carbon::now()->month)->count();
        $data['previous'] = Cdr::query()->whereMonth('start', Carbon::now()->subMonth()->month)->count();
        $data['total'] = $data['calls'] + $data['previous'];
        $data['difference'] = $data['calls'] - $data['previous'];
        $data['increase'] = $data['difference']/$data['total'] * 100;
        $data['sign'] = $data['increase'] < 0 ? '-' : '+';

        $data['answered_calls'] = Cdr::query()->whereMonth('start', Carbon::now()->month)->where('disposition', 'ANSWERED')->count();
        $data['answered_previous'] = Cdr::query()->whereMonth('start', Carbon::now()->subMonth()->month)->where('disposition', 'ANSWERED')->count();
        $data['answered_total'] = $data['answered_calls'] + $data['answered_previous'];
        $data['answered_difference'] = $data['answered_calls'] - $data['answered_previous'];
        $data['answered_increase'] = $data['answered_difference']/$data['answered_total'] * 100;
        $data['answered_sign'] = $data['answered_increase'] < 0 ? '-' : '+';

        $data['avg_calls'] = Cdr::query()->whereMonth('start', Carbon::now()->month)->average('billsec');
        $data['avg_previous'] = Cdr::query()->whereMonth('start', Carbon::now()->subMonth()->month)->average('billsec') ?? 0;
        $data['avg_total'] = $data['avg_calls'] + $data['avg_previous'];
        $data['avg_difference'] = $data['avg_calls'] - $data['avg_previous'];
        $data['avg_increase'] = $data['avg_difference']/$data['avg_total'] * 100;
        $data['avg_sign'] = $data['avg_increase'] < 0 ? '-' : '+';

        $data['max_calls'] = Cdr::query()->whereMonth('start', Carbon::now()->month)->max('billsec');
        $data['max_previous'] = Cdr::query()->whereMonth('start', Carbon::now()->subMonth()->month)->max('billsec') ?? 0;
        $data['max_total'] = $data['max_calls'] + $data['max_previous'];
        $data['max_difference'] = $data['max_calls'] - $data['max_previous'];
        $data['max_increase'] = $data['max_difference']/$data['max_total'] * 100;
        $data['max_sign'] = $data['max_increase'] < 0 ? '-' : '+';
        return view('admin.index', compact('data'));
    }
}
