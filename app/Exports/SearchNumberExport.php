<?php

namespace App\Exports;

use App\Cdr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SearchNumberExport implements FromCollection, WithHeadings
{
    private $number;
    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): \Illuminate\Support\Collection
    {
        return Cdr::query()->where('dst', 'like', "%{$this->number}%")->orderBy('start', 'desc')->with(['response_codes' => function($q) {
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
