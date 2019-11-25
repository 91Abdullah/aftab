<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\ReloadAction;

class CoreReload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Core reload to avoid CDR not logging bug';

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
        $manager = new ClientImpl($this->getAMIOptions());
        $action = new ReloadAction();
        $manager->open();
        $response = $manager->send($action);
        $manager->close();
        $this->info("INFO:  " . Carbon::now()->toDateTimeString() . " " . $response->getMessage());
    }

    private function getAMIOptions()
    {
        return [
            'host' => '192.168.0.200',
            'port' => 5038,
            'username' => 'manager_application',
            'password' => 'abdullah',
            'connect_timeout' => 1000,
            'read_timeout' => 1000,
            'scheme' => 'tcp://'
        ];
    }
}
