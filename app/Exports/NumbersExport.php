<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class NumbersExport implements FromCollection
{
    protected $numbers;

    public function __construct(Collection $collection)
    {
        $this->numbers = $collection;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->numbers;
    }
}
