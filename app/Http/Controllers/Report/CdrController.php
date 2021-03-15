<?php

namespace App\Http\Controllers\Report;

use App\Cdr;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use Yajra\DataTables\Facades\DataTables;

class CdrController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->start)->format('Y-m-d');
        $end = Carbon::parse($request->end)->format('Y-m-d');

        /*$cdrs = Cdr::whereBetween('start', [$start, $end])->get();*/
        //dd($cdrs);
        return view('report.cdr.index');
    }

    public function getReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::query()
            ->with('response_codes');

        /** @var User $user */
        $user = Auth::user();
        if($user->roles()->get(['name'])->contains('name', null, 'agent')) {
            $cdrs->where(function (Builder $query) use ($user) {
                $query->where('channel', 'like', "%{$user->endpoints()->first()->id}%")
                    ->orWhere('dstchannel', 'like', "%{$user->endpoints()->first()->id}%");
            });
        }

        $cdrs
            ->whereBetween(DB::raw('date(start)'), [$start, $end])
            ->orderBy('start')->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield', 'channel']);


        return DataTables::of($cdrs)
            ->addColumn('agent', function (Cdr $cdr) {
                $agent = explode("/", explode("-", $cdr->channel)[0])[1];
                return $agent == "TCL" ? /*"Transferred"*/ "" : $agent;
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? "NULL";
            })
            ->editColumn('dcontext', function (Cdr $cdr) {
                return $cdr->dcontext == "default" ? "Outgoing" : "Incoming";
            })
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    return "<a class='btn btn-danger' href='" . route('cdr.play', ['file' => $file_path]) . "'>Download</a>";
                } else
                    return "";
            })
            ->rawColumns(['recordingfile'])
            ->toJson();
    }
    public function getAutoGenReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::query()
            ->with('response_codes');

        /** @var User $user */
        $user = Auth::user();
        if($user->roles()->get(['name'])->contains('name', null, 'agent')) {
            $cdrs->where(function (Builder $query) use ($user) {
                $query->where('channel', 'like', "%{$user->endpoints()->first()->id}%")
                    ->orWhere('dstchannel', 'like', "%{$user->endpoints()->first()->id}%");
            });
        }

        $cdrs->join('numbers', 'cdr.dst', '=', 'numbers.number')
            ->whereBetween(DB::raw('date(start)'), [$start, $end])
            ->orderBy('start')->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield', 'channel']);


        return DataTables::of($cdrs)
            ->addColumn('agent', function (Cdr $cdr) {
                $agent = explode("/", explode("-", $cdr->channel)[0])[1];
                return $agent == "TCL" ? /*"Transferred"*/ "" : $agent;
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? "NULL";
            })
            ->editColumn('dcontext', function (Cdr $cdr) {
                return $cdr->dcontext == "default" ? "Outgoing" : "Incoming";
            })
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    return "<a class='btn btn-danger' href='" . route('cdr.play', ['file' => $file_path]) . "'>Download</a>";
                } else
                    return "";
            })
            ->rawColumns(['recordingfile'])
            ->toJson();
    }
    public function getSelfDialReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::query()
            ->with('response_codes');

        /** @var User $user */
        $user = Auth::user();
        if($user->roles()->get(['name'])->contains('name', null, 'agent')) {
            $cdrs->where(function (Builder $query) use ($user) {
                $query->where('channel', 'like', "%{$user->endpoints()->first()->id}%")
                    ->orWhere('dstchannel', 'like', "%{$user->endpoints()->first()->id}%");
            });
        }

        $cdrs->join('numbers', 'cdr.dst', '=', 'numbers.number', 'left outer')
            ->whereBetween(DB::raw('date(start)'), [$start, $end])
            ->orderBy('start')->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield', 'channel']);


        return DataTables::of($cdrs)
            ->addColumn('agent', function (Cdr $cdr) {
                $agent = explode("/", explode("-", $cdr->channel)[0])[1];
                return $agent == "TCL" ? /*"Transferred"*/ "" : $agent;
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? "NULL";
            })
            ->editColumn('dcontext', function (Cdr $cdr) {
                return $cdr->dcontext == "default" ? "Outgoing" : "Incoming";
            })
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    return "<a class='btn btn-danger' href='" . route('cdr.play', ['file' => $file_path]) . "'>Download</a>";
                } else
                    return "";
            })
            ->rawColumns(['recordingfile'])
            ->toJson();
    }
    public function getAutoGenReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::query()
            ->with('response_codes');

        /** @var User $user */
        $user = Auth::user();
        if($user->roles()->get(['name'])->contains('name', null, 'agent')) {
            $cdrs->where(function (Builder $query) use ($user) {
                $query->where('channel', 'like', "%{$user->endpoints()->first()->id}%")
                    ->orWhere('dstchannel', 'like', "%{$user->endpoints()->first()->id}%");
            });
        }

        $cdrs->join('numbers', 'cdr.dst', '=', 'numbers.number')
            ->whereBetween(DB::raw('date(start)'), [$start, $end])
            ->orderBy('start')->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield', 'channel']);


        return DataTables::of($cdrs)
            ->addColumn('agent', function (Cdr $cdr) {
                $agent = explode("/", explode("-", $cdr->channel)[0])[1];
                return $agent == "TCL" ? /*"Transferred"*/ "" : $agent;
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? "NULL";
            })
            ->editColumn('dcontext', function (Cdr $cdr) {
                return $cdr->dcontext == "default" ? "Outgoing" : "Incoming";
            })
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    return "<a class='btn btn-danger' href='" . route('cdr.play', ['file' => $file_path]) . "'>Download</a>";
                } else
                    return "";
            })
            ->rawColumns(['recordingfile'])
            ->toJson();
    }
    public function getSelfDialReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::query()
            ->with('response_codes');

        /** @var User $user */
        $user = Auth::user();
        if($user->roles()->get(['name'])->contains('name', null, 'agent')) {
            $cdrs->where(function (Builder $query) use ($user) {
                $query->where('channel', 'like', "%{$user->endpoints()->first()->id}%")
                    ->orWhere('dstchannel', 'like', "%{$user->endpoints()->first()->id}%");
            });
        }

        $cdrs->join('numbers', 'cdr.dst', '=', 'numbers.number', 'left outer')
            ->whereBetween(DB::raw('date(start)'), [$start, $end])
            ->orderBy('start')->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield', 'channel']);


        return DataTables::of($cdrs)
            ->addColumn('agent', function (Cdr $cdr) {
                $agent = explode("/", explode("-", $cdr->channel)[0])[1];
                return $agent == "TCL" ? /*"Transferred"*/ "" : $agent;
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? "NULL";
            })
            ->editColumn('dcontext', function (Cdr $cdr) {
                return $cdr->dcontext == "default" ? "Outgoing" : "Incoming";
            })
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    return "<a class='btn btn-danger' href='" . route('cdr.play', ['file' => $file_path]) . "'>Download</a>";
                } else
                    return "";
            })
            ->rawColumns(['recordingfile'])
            ->toJson();
    }
    public function playFile($file)
    {
        if($file == "" || !Str::contains($file, ['out', 'in'])) {
            return false;
        }
        $path = explode("_", $file);
        try {
            $path[1] = Str::length((string)$path[1]) == 1 ? "0$path[1]" : $path[1];
            $path[2] = Str::length((string)$path[2]) == 1 ? "0$path[2]" : $path[2];
            $route = Storage::disk('recordings')->download("$path[0]/$path[1]/$path[2]/$path[3]");
            return $route;
        } catch (FileNotFoundException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}
