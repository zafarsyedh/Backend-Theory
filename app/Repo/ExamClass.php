<?php

namespace App\Repo;
use App\Models\Category;
use App\Models\Exam;

class ExamClass implements Interfaces\ExamInterface
{

    public function getAllExamList()
    {
        $qry=Exam::with('invigilator','student','course');
        $qry=$qry->where('deleted_at',null)->orderBy('id','DESC');
        $qry=$qry->paginate(10);
        return $qry;
    }
    public function saveExam($request)
    {

        $exam=new Exam();
        $exam->std_id=$request->std_id;
        $exam->course_id=$request->course_id;
        $exam->invg_id=$request->invg_id;
        $exam->exam_date=$request->exam_date;
        $exam->status=($request->status==1?0:1);
        if($exam->save()){
            return $response='success';
        }else{
            return $response='Record not save due to some technical error';
        }
    }


    public function deleteExam($id)
    {
        // TODO: Implement deleteAddon() method.
        $std =Exam::find($id);
        if($std){
            $std->delete();
            return  1;
        }else{
            return "Rec not exist";
        }}


    public function editExam($id)
    {
        // TODO: Implement editAddon() method.
        return $category = Exam::find($id);
    }

    public function updateExam($request)
    {
        // TODO: Implement updateAddon() method.



        $exam=Exam::find($request->id);
        $exam->std_id=$request->std_id;
        $exam->course_id=$request->course_id;
        $exam->invg_id=$request->invg_id;
        $exam->exam_date=$request->exam_date;
        $exam->status=$request->status;
        $exam->save();
        return 1;
    }

    public function isStdExamSchedule($stdId)
    {

        // TODO: Implement isStdExamSchedule() method.
        $exam= Exam::where('std_id',$stdId)->where('status',0)->first();
        if($exam){
            return 1;
        }else{
            return 0;
        }
    }

    public function getStdExamInfo($stdId,$examType=null)
    {
        $qry=Exam::with('config','course','student');
        $qry=$qry->where('std_id',$stdId);
        $qry=$qry->where('status',0);
        ($examType==1)?$qry=$qry->where('is_attempt',0):'';
        $qry=$qry->latest('id')->first();
        return $qry;

    }



}
