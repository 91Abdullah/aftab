<?php

namespace App\Exports;

use App\Cdr;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CdrReportExport implements FromCollection, WithHeadings
{
    use Exportable;
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return Cdr::query()->whereDate('start', '>=', $this->start)->whereDate('end', '<=', $this->end)->with(['response_codes' => function($q) {
            $q->select(['id', 'name']);
        }])->get()->map(function ($row) {
            return [$row->dst, explode("-", explode("/", $row->channel)[1])[0] ?? null, $row->start, $row->end, $row->billsec, $row->disposition, $row->response_codes->first()->name ?? null];
        });
    }

    public function headings(): array
    {
        return [
            'Destination',
            'Agent',
            'Start time',
            'End time',
            'Duration',
            'Call status',
            'Code'
        ];
    }
}
