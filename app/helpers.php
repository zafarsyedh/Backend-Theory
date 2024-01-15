<?php

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee_Allownce;
use App\Models\Expense;
use App\Models\Lead;
use App\Models\LeadsMarketing;
use App\Models\Notification;
use App\Models\Purchase;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Transaction;
use App\Models\BankBranch;
use App\Models\BankSummry;
use App\Models\Client;
use App\Models\AccountSummary;
use App\Models\AssignedLeads;
use App\Models\ApprochedLeads;
use App\Models\Ledger;
use App\Models\SourceLeadsSettings;
use App\Models\TeamTarget;
use App\Models\Level_1;
use App\Models\Level_2;
use App\Models\Level_3;
use App\Models\Level_4;
use App\Models\Level_5;
use App\Models\CustomerServey;


function createAPIResponce($is_error, $code, $message, $content)
{


    $result = [];
    if ($is_error) {

        $result['success'] = false;
        $result['code'] = $code;
        $result['message'] = $message;
    } else {

        $result['success'] = true;
        $result['code'] = $code;

        if ($content == null) {

            $result['message'] = $message;
        } else {
            $result['data'] = $content;
        }
    }

    return $result;
}













