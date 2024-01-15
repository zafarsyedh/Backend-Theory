<?php
namespace App\Repo\Interfaces;

interface ResultInterface{

    public function getPracticeTestResult($stdId);
    public function getExamResult($request);
    public function printExamResult($request);
    public function getExamResultForAdminView($stdId);
    public function getExamResultDetail($examId);

    public function courseWiseResults($course_id);
    public function countOverAllResults();




}
