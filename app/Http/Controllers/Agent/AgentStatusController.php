<?php

namespace App\Http\Controllers\Agent;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PAMI\Client\Exception\ClientException;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueUnpauseAction;

class AgentStatusController extends Controller
{
    public function readyAgent(Request $request)
    {
        $request->validate([
            'agent' => ['required', 'exists:ps_endpoints,id']
        ]);

        $agent = $request->agent;
        $client = new ClientImpl($this->getOptions());
        $action = new QueueUnpauseAction("PJSIP/$agent");
        try {
            $client->open();
            $response = $client->send($action);
            $client->close();

            if($response->getMessage() === "Interface unpaused successfully") {
                return response()->json(['message' => $response->getMessage()], 200);
            }

            return response()->json(['message' => $response->getMessage()], 200);
        } catch (ClientException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notReadyAgent(Request $request)
    {
        $request->validate([
            'agent' => ['required', 'exists:ps_endpoints,id']
        ]);

        $agent = $request->agent;
        $client = new ClientImpl($this->getOptions());
        $action = new QueuePauseAction("PJSIP/$agent");
        try {
            $client->open();
            $response = $client->send($action);
            $client->close();

            if($response->getMessage() === "Interface paused successfully") {
                return response()->json(['message' => $response->getMessage()], 200);
            }

            return response()->json(['message' => $response->getMessage()], 500);
        } catch (ClientException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAgentStatus(Request $request)
    {
        $queue = "CCQ10";
        $request->validate([
            'agent' => ['required', 'exists:ps_endpoints,id']
        ]);
        $agent = $request->agent;
        $client = new ClientImpl($this->getOptions());
        $action = new QueueStatusAction($queue, "PJSIP/$agent");

        try {
            $client->open();
            $response = $client->send($action);
            $client->close();
            //return dd($response->getEvents()[1]->getKeys());
            return response()->json(['message' => $response->getEvents()[1]->getKeys()], 200);
        } catch (ClientException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getOptions()
    {
        return [
            'host' => Setting::query()->where('key', 'server_address')->first()->value ?? '127.0.0.1',
            'port' => '5038',
            'username' => 'defaultapp',
            'secret' => 'randomsecretstring',
            'connect_timeout' => 5000,
            'read_timeout' => 5000,
            'scheme' => 'tcp://'
        ];
    }
}
