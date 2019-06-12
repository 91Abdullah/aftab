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

        $cdrs = Cdr::whereBetween(DB::raw('date(start)'), [$start, $end])->get(['src', 'dst', 'start', 'answer', 'end', 'duration', 'disposition', 'recordingfile']);

        //dd($cdrs);
        return DataTables::of($cdrs)
            ->editColumn('recordingfile', function (Cdr $cdr) {
                if($cdr->disposition == "ANSWERED") {
                    $date = Carbon::parse($cdr->start);
                    $year = $date->year;
                    $month = $date->month;
                    $day = $date->day;
                    $file_path = $year . "_" . $month . "_" . $day . "_" . $cdr->recordingfile;
                    $route = $this->playFile($file_path);
                    return $route;
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
            $route = Storage::disk('recordings')->download("$path[0]/$path[1]/$path[2]/$path[3]");
            return "<a class='btn btn-danger' href=$route>Download</a>";
        } catch (FileNotFoundException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}
