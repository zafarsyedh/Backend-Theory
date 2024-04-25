<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        $users = User::get();

        return view('users', compact('users'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import()
    {
        Excel::import(new UsersImport,request()->file('file'));

        return back();
    }

    public  function testDate()
    {




        $date = '2024-04-25 12:50:13';
        $startDate = Carbon::parse($date);
        $endDate = Carbon::now();
        $diff = $startDate->diff($endDate);

        $mainDiff= $diff->h.':'. $diff->i .':'. $diff->s;
        return $mainDiff;
        return "Difference: $hours hours, $minutes minutes, $seconds seconds";

    }
}
