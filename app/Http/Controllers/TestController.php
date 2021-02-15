<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use PAMI\Client\Exception\ClientException;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\OriginateAction;

class TestController extends Controller
{
    public function test(Request $request)
    {
        return dd(User::query()->where('name', 'Test Admin')->first());
    }

    private function getOptions(): array
    {
        return [
            'host' => '192.168.144.202',
            'port' => 5038,
            'scheme' => 'tcp://',
            'username' => 'defaultapp',
            'secret' => 'randomsecretstring',
            'connect_timeout' => 10000,
            'read_timeout' => 10000
        ];
    }
}
