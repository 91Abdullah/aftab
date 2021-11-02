<?php

namespace App\Http\Controllers\Report;

use App\Cdr;
use App\Exports\CdrReportExport;
use App\Exports\SearchNumberExport;
use App\ResponseCode;
use App\Role;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use function foo\func;


class CdrController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->start)->format('Y-m-d');
        $end = Carbon::parse($request->end)->format('Y-m-d');

        $cdrs = Cdr::whereBetween('start', [$start, $end])->get();
        //dd($cdrs);
        return view('report.cdr.index', compact('cdrs'));
    }

    public function searchNumber(Request $request)
    {
        return view('report.cdr.searchNumber');
    }

    public function getSearchNumber(Request $request)
    {
        $data = Cdr::query()->where('dst', 'like', "%{$request->number}%")->orderBy('start', 'desc')->paginate(50);
        return view('report.cdr.searchNumber', compact('data'));
    }

    public function getDownloadSearchNumberReport(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new SearchNumberExport($request->number), 'search_number.xlsx');
    }

    public function getDownloadReport()
    {
        return view('report.cdr.download');
    }

    public function downloadReport(Request $request)
    {
        if($request->has('start') && $request->has('end')) {
            return Excel::download(new CdrReportExport($request->start, $request->end), 'cdr_report.xlsx');
        } else {
            return view('report.cdr.download')->with("status", "Invalid date/time entered.");
        }
    }

    public function getReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::whereBetween(DB::raw('date(start)'), [$start, $end])->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile', 'userfield']);

        return DataTables::of($cdrs)
            ->editColumn('clid', function (Cdr $cdr) {
                preg_match('/"([^"]+)"/', $cdr->clid, $m);
                return $m[1];
            })
            ->addColumn('code', function (Cdr $cdr) {
                return $cdr->response_codes()->first()->name ?? null;
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
            ->select('*')
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

        $cdrs->join('numbers as num', 'cdr.dst', '=', 'num.number', 'left outer')
        ->whereBetween(DB::raw('date(start)'), [$start, $end])
        ->whereNull('num.number')
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

    public function getFile(Request $request)
    {
        try {
            $path = explode("-", $request->file);
            $date = Carbon::parse($path[3]);
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');
            $file = $request->file;
            return Storage::disk('recordings')->download("{$year}/{$month}/{$day}/$file");
        } catch (FileNotFoundException | Exception $e) {
            return $e->getMessage();
        }
    }
}
