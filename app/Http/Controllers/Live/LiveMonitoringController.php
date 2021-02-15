<?php

namespace App\Http\Controllers\Live;

use App\Setting;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PAMI\Client\Exception\ClientException;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Event\CoreShowChannelEvent;

class LiveMonitoringController extends Controller
{
    private function getOptions()
    {
        $host = Setting::query()->where('key', 'server_address')->first()->value ?? "192.168.144.202";
        return [
            'host' => $host,
            'port' => 5038,
            'scheme' => 'tcp://',
            'username' => 'defaultapp',
            'secret' => 'randomsecretstring',
            'connect_timeout' => 10000,
            'read_timeout' => 10000
        ];
    }

    private function getTrunkEndpoint(): string
    {
        if(Setting::query()->where('key', 'endpoint')->first()) {
            $trunk = Setting::query()->where('key', 'endpoint')->first()->value;
        } else {
            $trunk = 'PJSIP/TCL-endpoint';
        }
        return $trunk;
    }

    public function getUser(User $user): \Illuminate\Http\JsonResponse
    {
        $user = $user->endpoints()->first()->id;
        $server = Setting::query()->where('key', 'server_address')->first()->value;
        $res = ['user' => $user, 'server' => $server];
        return response()->json($res);
    }

    public function getServer(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Setting::query()->where('key', 'server_address')->first()->value);
    }

    public function listenThisCall(Request $request)
    {
        $callerID = ucfirst($request->mode);
        $self = $request->selfAgent;
        $action = new OriginateAction("PJSIP/{$self}");
        $action->setContext("default");
        $action->setPriority("1");
        $action->setCallerId("{$callerID} <{$request->agent}>");
        if($request->mode === "listen")
            $action->setExtension("911{$request->agent}");
        else if($request->mode === "whisper")
            $action->setExtension("912{$request->agent}");
        else if($request->mode === "barge")
            $action->setExtension("913{$request->agent}");
        $client = new ClientImpl($this->getOptions());
        try {
            $client->open();
            $response = $client->send($action);
        } catch (\Exception $exception) {
            $client->close();
            return response()->json($exception->getMessage(), 500);
        }
        $client->close();
        return response()->json($response->getMessage(), 200);
    }

    public function getLiveCalls(Request $request)
    {
        $client = new ClientImpl($this->getOptions());
        $action = new CoreShowChannelsAction();
        try {
            $client->open();
            $response = $client->send($action);
        } catch (ClientException $e) {
            return response()->json($e->getMessage(), 500);
        }
        $client->close();
        $outgoingChannels = [];
        foreach ($response->getEvents() as $key => $event) {
            if($event instanceof CoreShowChannelEvent && str_contains($event->getKey('channel'), $this->getTrunkEndpoint())) {
                $outgoingChannels[] = [
                    'channel' => $event->getKey('channel'),
                    'channelstate' => $event->getKey('channelstate'),
                    'channelstatedesc' => $event->getKey('channelstatedesc'),
                    'calleridnum' => $event->getKey('calleridnum'),
                    'connectedlinenum' => $event->getKey('connectedlinenum'),
                    'connectedlinename' => $event->getKey('connectedlinename'),
                    'uniqueid' => $event->getKey('uniqueid'),
                    'application' => $event->getKey('application'),
                    'applicationdata' => $event->getKey('applicationdata'),
                    'duration' => $event->getKey('duration'),
                ];
                /*$outgoingChannels[$key]['channel'] = $event->getKey('channel');
                $outgoingChannels[$key]['channelstate'] = $event->getKey('channelstate');
                $outgoingChannels[$key]['channelstatedesc'] = $event->getKey('channelstatedesc');
                $outgoingChannels[$key]['calleridnum'] = $event->getKey('calleridnum');
                $outgoingChannels[$key]['connectedlinenum'] = $event->getKey('connectedlinenum');
                $outgoingChannels[$key]['connectedlinename'] = $event->getKey('connectedlinename');
                $outgoingChannels[$key]['uniqueid'] = $event->getKey('uniqueid');
                $outgoingChannels[$key]['application'] = $event->getKey('application');
                $outgoingChannels[$key]['applicationdata'] = $event->getKey('applicationdata');
                $outgoingChannels[$key]['duration'] = $event->getKey('duration');*/
            }
        }
        return response()->json($outgoingChannels, 200);
    }
}
