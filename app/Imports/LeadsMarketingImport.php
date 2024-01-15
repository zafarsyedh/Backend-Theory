<?php

namespace App\Imports;

use App\Models\LeadSetting;
use App\Models\LeadsMarketing;
use App\Models\SourceLeadsSettings;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class LeadsMarketingImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if ($row['name']) {
            if (!LeadsMarketing::where('contact', $row['contact'])->first()) {
                (is_numeric($row['city'])) ? $city = $row['city'] : $city = 1;
                (is_numeric($row['source'])) ? $source = $row['source'] : $source = 1;


                $leads = new LeadsMarketing();
                $leads->name = $row['name'];
                $leads->email = $row['email'];
                $leads->contact = $row['contact'];
                $leads->city_id = $city;
                $leads->platform_id = $source;
                $leads->address = $row['address'];
                $leads->date = $row['date'];
                $leads->lead_type = $row['lead_type'];
                $leads->interest = $row['interest'];
                $leads->user_id = Auth::user()->id;
                $leads->upload_via ='excel';
                $leads->save();


            }
        }
    }
}


