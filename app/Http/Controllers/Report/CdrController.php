<?php

namespace App\Http\Controllers\Report;

use App\Cdr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $cdrs = Cdr::whereBetween('start', [$start, $end])->get();
        //dd($cdrs);
        return view('report.cdr.index', compact('cdrs'));
    }

    public function getReport(Request $request)
    {
        $start = Carbon::parse($request->start_date)->format('Y-m-d ');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        $cdrs = Cdr::whereBetween(DB::raw('date(start)'), [$start, $end])->get(['src', 'dst', 'clid', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile']);

        return DataTables::of($cdrs)
            ->editColumn('clid', function (Cdr $cdr) {
                preg_match('/"([^"]+)"/', $cdr->clid, $m);
                return $m[1];
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
