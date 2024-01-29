<?php
namespace App\Repo\Interfaces;

interface ExamInterface{

    public function saveExamQuestion($request);
    public function savePracticeQuestion($request);
    public function getScheduleExamList();


}
