<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use DB;
use App\Events\MarkAttendanceEvent;
use App\Models\Loged_history;
use Auth;
use Carbon\Carbon;


class LoginController extends Controller
{


    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'status' => 1))) {
//            $this->findDistance($request);
            Loged_history::createLogedInHistory('web', $request->latitude, $request->longitude, Auth::user()->id, $request->address,);
            return redirect('/');
        } else {

            return redirect()->route('login')
                ->with('error', 'Invalid authentication!');
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }


    public function findDistance($request)
    {

        if ($request->latitude && $request->longitude) {
            $qry = Company::query();
            $qry = $qry->select("*", DB::raw("6371 * acos(cos(radians(" . $request->latitude . "))
                                * cos(radians(lat)) * cos(radians(lang) - radians(" . $request->longitude . "))
                                + sin(radians(" . $request->latitude . ")) * sin(radians(lat))) AS distance"));

            $qry = $qry->having('distance', '<', 20);
            $qry = $qry->orderBy('distance', 'asc');
            $qry = $qry->first();
            if ($qry) {
                $mile = $qry->distance;
                $meter = $mile * 1609.344;

                if ($meter <= 500) {

                    event(new MarkAttendanceEvent());
                }
            }
        }
    }
}
