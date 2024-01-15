<?php

namespace App\Imports;

use App\Models\EmailCompagin;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        if (!EmailCompagin::where('email', $row['email'])->first()) {
            $email = new EmailCompagin();
            $email->name = $row['name'];
            $email->email = $row['email'];
            $email->city = $row['city'];
            $email->save();
        }
    }
}
