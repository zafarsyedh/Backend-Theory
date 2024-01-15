<?php

namespace App\Exports;

use App\Models\LeadsMarketing;
use Maatwebsite\Excel\Concerns\FromCollection;

class LeadsMarketingExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LeadsMarketing::all();
    }
}
