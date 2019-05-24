<?php

namespace App\Http\Controllers;

use App\GenNum;
use App\Number;
use App\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AgentController extends Controller
{
    public function index()
    {
        return view('front.agent');
    }

    public function test()
    {
        $startTime = Carbon::now();
        $rustart = getrusage();

        $total_numbers = 100;
        $randomNumbers  = [];

        for($i = 0; $i < $total_numbers; $i++) {
            /*$randomNumbers[$i] = [
                "number" => "03" . mt_rand(0, 4) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];*/

            $firstDigit = "0";
            $levelOne = mt_rand(30, 34);

            $levelTwo = mt_rand(0, 9);

            switch ($levelOne) {
                case 30:
                    $levelTwo = mt_rand(0, 9);
                    break;
                case 31:
                    $levelTwo = mt_rand(0, 9);
                    break;
                case 32:
                    $levelTwo = mt_rand(0, 6);
                    break;
                case 33:
                    $levelTwo = mt_rand(0, 8);
                    break;
                case 34:
                    $levelTwo = mt_rand(0, 9);
                    break;
                default:
                    break;
            }

            $levelThree = $num_str = sprintf("%07d", mt_rand(1, 9999999));

            $randomNumbers[$i] = [
                "number" => $firstDigit . $levelOne . $levelTwo . $levelThree,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
        }

        $finalNums = collect($randomNumbers)->unique("number");

        //return dd($finalNums->toArray());
        //Number::insert($finalNums->toArray());
        //return dd(Number::all(['number'])->toArray());

        $totalNums = Number::all(['number'])->toArray();

        $uniqueNums = $finalNums->whereNotIn('number', $totalNums);

        $ru = getrusage();

        $endTime = Carbon::now();

        $proc = Process::create([
            "desc" => "This process used " . $this->rutime($ru, $rustart, "utime") . " ms for its computations. It spent " . $this->rutime($ru, $rustart, "stime") . " ms in system calls",
            "starttime" => $startTime,
            "endtime" => $endTime,
            "execution_time" => $this->rutime($ru, $rustart, "utime"),
            "call_time" => $this->rutime($ru, $rustart, "stime"),
            "generated_nums" => $uniqueNums->count(),
            "db_nums" => count($totalNums)
        ]);

        //$genNums = GenNum::insert($uniqueNums->toArray());
        return dd($uniqueNums);
    }

    function rutime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
    }
}
