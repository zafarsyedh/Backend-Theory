<?php
namespace App\Repo\Interfaces;

interface ExamInterface{

    public function saveExamQuestion($request);
    public function savePracticeQuestion($request);
    public function getScheduleExamList();
    public function createExam($request,$data);
    public function updateExam($request);
    public function deleteExam($id);
    public function checkExamStatus($stdData);


}
