<?php

namespace App\Console\Commands;

use App\Cdr;
use App\Imports\CdrImport;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:run {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asterisk CDR Parser';

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
        $data = Excel::import(new CdrImport, $this->argument('file'));
        $this->info($data);
    }
}
