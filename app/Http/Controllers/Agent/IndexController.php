<?php

namespace App\Http\Controllers\Agent;

use App\GenNum;
use App\Number;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
