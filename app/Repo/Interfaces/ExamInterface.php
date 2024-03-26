<?php
namespace App\Repo\Interfaces;

interface ExamInterface{

    public function saveExamQuestion($request);
    public function savePracticeQuestion($request);
    public function getScheduleExamList($request);
    public function getAllResultsList();
    public function getPracticeResult();
    public function createExam($request,$data);
    public function updateExam($request);
    public function deleteExam($id);
    public function checkExamStatus($stdData,$examType);
    public function updateAttemptStatus($attemptId);
    public function updateExamScheduleStatus($examScheduleId,$status);
    public function getAttemptInfo($attemptId);
    public function getSolvedQuestionAccordingAttempt($attemptId);
    public function createResult($data);
    public function getExamWiseResult($examId);
    public function checkPracticeType($request);
    public function checkExamStartOrNot($id);
    public function sendEmail($trafficId,$examId,$result,$student);
    public function sendSms($student,$result);
    public function updateMailAndSmsStatus($resultId,$type);
}
