<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\StudentRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\StudentResource;
use App\Models\Exam;
use App\Repo\Interfaces\StudentInterface;
use App\Repo\Interfaces\UserInterface;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public  $student;
    public function __construct(StudentInterface $student)
    {
        $this->student=$student;

    }
    public function index(){

        try {
            $response=$this->student->getAllStudent();
            if($response->count() > 0){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $response);
            }else{
                $response= Helper::createAPIResponce($is_error = true, $code = 204, $message = 'not content', $response);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    public function create(StudentRequest $request){

        $res=$this->student->saveStudent($request);
        if($res=='success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 204, $message =$res, $res);
        }
        return response()->json($response);
    }
    public function deleteStudent(Request $request){
        $id=$request->id;
        $response=$this->student->deleteStudent($id);
        if($response==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 201, $message = 'Record deleted', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = false, $code = 206, $message = $response, $response);
        }
        return response()->json($response);
    }
    public function editStudent(Request $request){
          $id=$request->id;
        $res=$this->student->editStudent($id);
        if($res){

            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record found', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $res);

        }
        return response()->json($response);
    }
    public function updateStudent(Request $request){
         $request->all();
        $res=$this->student->updateStudent($request);
        if($res==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record updated successfully', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message =$res,$res);
        }
        return response()->json($response);
    }


    public function getAllStdDropdown(){
        $response=$this->student->getAllStdDropdown();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 204, $message = 'not content', $response);
        }
        return response()->json($response);
    }

    public function login(Request $request)
    {

        if(!$std=$this->student->getStdWithTrafficId($request->std_traffic_id)){
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message ='This traffic id not exist in our records',$std);
            return response()->json($response);
        }



        if(!$exam=Exam::where('std_id',$std->id)->latest('id')->first()){
            $response= Helper::createAPIResponce($is_error = true, $code = 505, $message ='Exam not schedule for this student',$exam);
            return response()->json($response);
        }


        if (!auth()->attempt(array('user_id' =>$request->supervisor_id,'password' => $request->password, 'status' => 1,'is_take_test'=>1,'is_deleted'=>0))) {
            Helper::sendError('Credentials error!');
        }

        $this->student->studentLogedHistory($std->id,auth()->user()->id);
        // send response
        return new StudentResource(auth()->user(),$std);
    }
}



