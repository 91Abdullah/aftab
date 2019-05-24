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
        $randomNumbers  = [];

        $genCount = GenNum::count();

        $total_numbers = $total_numbers - $genCount;

        for($i = 0; $i < $total_numbers; $i++) {
            $randomNumbers[$i] = [
                "number" => "03" . mt_rand(0, 4) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
        }

        $finalNums = collect($randomNumbers)->unique("number");

        //return dd($finalNums->toArray());
        //Number::insert($finalNums->toArray())
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

        $genNums = GenNum::insert($uniqueNums->toArray());

    }
}
