<?php
namespace App\Repo\Interfaces;

interface ExamInterface{

    public function getAllExamList();
    public function saveExam($request);
    public function deleteExam($id);

    public function editExam($id);
    public function updateExam($request);
    public function isStdExamSchedule($stdId);
    public function getStdExamInfo($stdId,$examType);




}
