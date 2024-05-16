<?php
namespace App\Repo\Interfaces;

interface ExamInterface{

    public function saveExamQuestion($request);
    public function savePracticeQuestion($request);
    public function getScheduleExamList($request);
    public function getAllResultsList($request);
    public function getPracticeResult($request);
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
    public function sendEmail($trafficId,$examId,$result,$student,$mailData);
    public function sendSms($student,$result,$otpText);
    public function updateMailAndSmsStatus($resultId,$type);
    public function storeSmsEmailLog($examId,$type,$isSend,$content);
    public function getLogs($request);
    public function getExamAttemptInfo($examId);

    public function getExamIdOnTheBaseOfTrafficIdNumber($trafficId);
}
