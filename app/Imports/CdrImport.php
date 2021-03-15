<?php

namespace App\Imports;

use App\Cdr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Importer;

class CdrImport implements ToCollection, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if($item[10] == NULL) {
                dd($item);
            }
            Cdr::updateOrCreate([
                'accountcode' => $item[0],
                'src' => $item[1],
                'dst' => $item[2],
                'dcontext' => $item[3],
                'clid' => $item[4],
                'channel' => $item[5],
                'dstchannel' => $item[6],
                'lastapp' => $item[7],
                'lastdata' => $item[8],
                'start' => $item[9] !== null ? Carbon::parse($item[9]) : '',
                'answer' => $item[10] !== NULL ? Carbon::parse($item[10]) : NULL,
                'end' => $item[11] !== null ? Carbon::parse($item[11]) : '',
                'duration' => $item[12],
                'billsec' => $item[13],
                'disposition' => $item[14],
                'amaflags' => $item[15],
                'userfield' => $item[16],
                'uniqueid' => $item[17],
                'linkedid' => $item[18],
                'peeraccount' => $item[19],
                'sequence' => $item[20],
                'recordingfile' => $item[21] ?? ''
            ]);
        }
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
