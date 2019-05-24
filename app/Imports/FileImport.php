<?php

namespace App\Imports;

use App\ListNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class FileImport implements ToModel, WithValidation
{
    private $parent_id;

    public function __construct($parent)
    {
        $this->parent_id = $parent;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ListNumber([
            'number' => $row[0],
            'upload_list_id' => $this->parent_id
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required','regex:/(03)[0-9]{9}/']
        ];
    }
}
