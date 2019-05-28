<?php

namespace App\Console\Commands;

use App\GenNum;
use App\Number;
use App\Process;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GenerateNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:numbers {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commands to generate user defined numbers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $rustart = getrusage();

        $total_numbers = $this->argument('count');
        $genCount = GenNum::count();
        $minValueToMaintain = $total_numbers;
        $total_numbers = $total_numbers - $genCount;


        // Check count again
        do {
            $finalNums = $this->generateUniqueNumbers($total_numbers);
            $totalNums = Number::where('created_at', '>', Carbon::now()->subMonths(6))->get(['number'])->toArray();
            $uniqueNums = $finalNums->whereNotIn('number', $totalNums);
        } while ($uniqueNums->count() + $genCount < $minValueToMaintain);

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

        $genNums = GenNum::insert($uniqueNums->toArray());

    }

    function rutime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
    }

    function generateUniqueNumbers(int $total_numbers)
    {
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
                    $levelTwo = mt_rand(0, 4);
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

            $levelThree = $num_str = sprintf("%07d", mt_rand(0, 9999999));

            $randomNumbers[$i] = [
                "number" => $firstDigit . $levelOne . $levelTwo . $levelThree,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
        }



        return $finalNums = collect($randomNumbers)->unique("number");
    }
}
