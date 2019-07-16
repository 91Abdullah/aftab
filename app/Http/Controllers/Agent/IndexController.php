<?php

namespace App\Http\Controllers\Agent;

use App\Cdr;
use App\GenNum;
use App\Number;
use App\ResponseCode;
use App\ScheduleCall;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IndexController extends Controller
{
    public function index()
    {
        $random_mode = Setting::where('key', 'random_mode')->first()->value;
        $responseCodes = ResponseCode::pluck('name', 'code');
        return view('admin.agent.index', compact('random_mode', 'responseCodes'));
    }

    public function getScheduledCallsTable()
    {
        $user = Auth::user();
        return DataTables::collection($user->schedule_calls->take(5))
            ->editColumn('schedule_time', function (ScheduleCall $call) {
                return $call->schedule_time->format("d-m-Y H:i:s");
            })
            ->editColumn('status', function (ScheduleCall $call) {
                $status = $call->status == true ? 'DONE' : 'PENDING';
                $label = $call->status == true ? 'badge badge-success' : 'badge badge-danger';
                return "<span class='" . $label . "'>$status</span>";
            })
            ->editColumn('updated_at', function (ScheduleCall $call) {
                return $call->status == true ? $call->updated_at->diffForHumans() : "";
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getGenNumber()
    {
        // Check any pending scheduled call first

        $user = Auth::user();
        if($call = ScheduleCall::expired($user)->first()) {
            $call->status = true;
            $call->save();
            return response()->json($call->number, 200);
        }

        $currentNumber = GenNum::first();
        $number = $currentNumber->number;
        $movedNumber = Number::create([
            'number' => $currentNumber->number,
            'status' => 1
        ]);
        $currentNumber->delete();

        return response()->json($number, 200);
    }

    public function getRecentCalls(Request $request)
    {
        $user_id = $request->user_id;
        return DataTables::collection(Cdr::where([
            ['disposition', 'ANSWERED'],
            ['clid', 'like', "%$user_id%"]
        ])
            ->latest('start')->take(5)->get())
            ->editColumn('clid', function (Cdr $cdr) {
                preg_match('/"([^"]+)"/', $cdr->clid, $m);
                return $m[1];
            })
            ->make(true);
    }

    public function scheduleCall(Request $request)
    {
        $dt = Carbon::parse($request->schedule_time);
        $user = $request->user_id;
        $number = $request->number;

        $validator = Validator::make($request->all(), [
            'schedule_time' => 'required',
            'user_id' => 'required|exists:users,id',
            'number' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $user = User::findOrFail($user);
        $user->schedule_calls()->create($request->all());
        return response()->json(['success' => 'Schedule has been created.']);
    }
}
