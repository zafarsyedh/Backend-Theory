<?php

namespace App\Repo;
use App\Models\Category;
use App\Models\Exam;
use App\Models\Studen;
use App\Models\Student;
use App\Models\StudentLogedHistory;
use App\Traits\HandleFiles;

class StudentClass implements Interfaces\StudentInterface
{
use HandleFiles;
public $path='student-images/';
public  $imagName = '';
    public function getAllStudent()
    {
          $qry = Student::query();
            $qry=$qry->with(['result' => function ($query) {
             $query->latest('id')->first();
         }]);

        $qry=$qry->with(['history' => function ($query) {
            $query->latest('id')->first();
        }]);

       $qry=$qry->orderBy('id','DESC');
        $qry=$qry->paginate(10);
        return $qry;

    }

    public function saveStudent($request)
    {
        // TODO: Implement saveAddon() method.

        if(Student::where('tarffic_id',$request->traffic_id)->where('deleted_at',null)->first()){
            return $response='This record already exist';
        }



        if ($file = $request->file('file')) {
            $this->imagName = $this->handleFiles($file, $this->path);
        }

        $std=new Student();
        $std->name=$request->name;
        $std->l_name=$request->l_name;
        $std->email =$request->email;
        $std->phone=$request->phone;
        $std->tarffic_id=$request->traffic_id;
        $std->nationality_id=$request->nationality_id;
        $std->gender=$request->gender;
        $std->status=$request->status;
        $std->image=$this->imagName;
        if($std->save()){
            return $response='success';
        }else{
            return $response='Record not save due to some technical error';
        }
    }

    public function deleteStudent($id)
    {
        // TODO: Implement deleteAddon() method.
        $std =Student::find($id);
        if($std){
        $std->delete();
        return  1;
    }else{
            return "Rec not exist";
        }}

    public function editStudent($id)
    {
        // TODO: Implement editAddon() method.
        return $std = Student::find($id);
    }

    public function updateStudent($request)
    {
        // TODO: Implement updateAddon() method.
        $std = Student::find($request->id);
        if ($std) {

            if ($file = $request->file('file')) {
                $this->imagName = $this->handleFiles($file, $this->path);
            }

            $std->name = $request->name;
            $std->l_name = $request->l_name;
            $std->email = $request->email;
            $std->phone = $request->phone;
            $std->tarffic_id = $request->traffic_id;
            $std->nationality_id = $request->nationality_id;
            $std->gender = $request->gender;
            $std->status = $request->status;
            ($this->imagName!='')? $std->image = $this->imagName:'';
            $std->save();
            return 1;
        }
    }

    public function getAllStdDropdown()
    {
        $qry=Student::query();
        $qry=$qry->where('status',1);
        $qry=$qry->orderBy('id','DESC');
        $qry=$qry->get();
        return $qry;

    }

    public function getStdWithTrafficId($trafficId)
    {
        // TODO: Implement editAddon() method.
        return $std = Student::where('tarffic_id',$trafficId)->where('status',1)->first();
    }

    public function studentLogedHistory($stdId,$staffId)
    {
        $log=new StudentLogedHistory();
        $log->std_id=$stdId;
        $log->staff_id=$staffId;
        $log->save();
    }
    public function chekStdExamSchudle($stdId)
    {
        // TODO: Implement chekStdExamSchudle() method.
        return $std = Exam::where('std_id',$stdId)->where('status',0)->where('is_attempt',0)->latest('id')->first();
    }
}
