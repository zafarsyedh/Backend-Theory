<?php

namespace App\Imports;

use App\Models\CustomerServey;
use App\Models\LeadSetting;
use App\Models\LeadsMarketing;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerSurvryImport implements ToModel,WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($row['name']) {
            if (!CustomerServey::where('contact', $row['contact'])->first()) {
                $survey = new CustomerServey();
                $survey->name = $row['name'];;
                $survey->contact = $row['contact'];
                $survey->project = $row['project'];
                $survey->date = $row['date'];
                $survey->size = $row['marla'];
                $survey->save();


            }
        }
    }
}
