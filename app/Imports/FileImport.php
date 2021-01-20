<?php

namespace App\Imports;

use App\ListNumber;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FileImport implements ToModel, WithValidation, WithHeadingRow
{
    use Importable;
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
        /*return new ListNumber([
            'number' => $row[0],
            'upload_list_id' => $this->parent_id
        ]);*/

        return new ListNumber([
            'number' => $row['number'],
            'name' => $row['name'],
            'city' => $row['city'],
            'upload_list_id' => $this->parent_id
        ]);
    }

    public function rules(): array
    {
        return [
            'number' => ['required','regex:/(3)[0-9]{8}/'],
            'name' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
        ];
    }
}
