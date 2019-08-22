<?php

namespace App\Http\Controllers\Report;

use App\Cdr;
use App\ResponseCode;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ResponseCodeReportController extends Controller
{
    public function index()
    {
        $columns = [
            ["data" => "id", "name" => "id"],
            ["data" => "agent", "name" => "agent"],
            ["data" => "calls", "name" => "calls"]
        ];
        $codes = ResponseCode::pluck('name');
        $mergedArray = [];
        foreach ($codes as $code) {
            $mergedArray[] = ["data" => $code, "name" => $code];
        }
        $columns = collect(array_merge($columns, $mergedArray))->toJson();
        return view('report.code.index', compact('columns'));
    }

    public function getReport(Request $request)
    {
        if($request->ajax())
        {
            $start = Carbon::parse($request->start_date)->format('Y-m-d ');
            $end = Carbon::parse($request->end_date)->format('Y-m-d');

            $agents = Role::where("name", "agent")->first()->users->all();
            $codes = ResponseCode::all();
            $data = [];

            foreach ($agents as $index => $agent) {
                $calls = Cdr::whereBetween(DB::raw('date(start)'), [$start, $end])->where("channel", "like", "PJSIP/" . $agent->endpoints()->first()->id . "%")->count();
                $codeArray = [];

                foreach ($codes as $codeIndex => $code) {
                    $codeArray[$code->name] = $code->cdrs()->whereBetween(DB::raw('date(start)'), [$start, $end])->where("channel", "like", "PJSIP/" . $agent->endpoints()->first()->id . "%")->count();
                }

                $data[] = array_merge([
                    "agent" => $agent->name,
                    "calls" => $calls,
                    "id" => $agent->endpoints()->first()->id
                ], $codeArray);
            }

            return DataTables::of($data)
                ->toJson();
        }
    }
}
