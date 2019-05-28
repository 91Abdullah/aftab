<?php

namespace App\Http\Controllers\Agent;

use App\Cdr;
use App\GenNum;
use App\Number;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin.agent.index');
    }

    public function getGenNumber()
    {
        $currentNumber = GenNum::first();
        $number = $currentNumber->number;
        $movedNumber = Number::create([
            'number' => $currentNumber->number,
            'status' => 1
        ]);
        $currentNumber->delete();

        return response()->json($number, 200);
    }

    public function getRecentCalls()
    {
        return Cdr::where('disposition', 'ANSWERED')->latest('start')->take(5)->get(['src', 'dst', 'duration', 'disposition', 'recordingfile'])->toJson(200);
    }
}
