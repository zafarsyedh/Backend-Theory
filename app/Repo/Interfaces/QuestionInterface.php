<?php
namespace App\Repo\Interfaces;

interface QuestionInterface{


    public function createQuestions($request);
    public function saveQuestionTranslation($request);
    public function deleteQuestion($id);
    public function findQuestionById($id);
    public function getQuestionTranslationsById($id);
    public function createNewAttempt($request);
    public function createAttemptAndSolveQuestion($request);
    public function   getMovedQuestionForTheoryPractice($request,$attemptId,$purpose);
    public function getAllCourseWiseRandomQuestion($courseId,$qLang,$limit);
    public function getCommonQuestion($qLang,$limit);
    public function getSpecificQuestion($courseId,$qLang,$limit);
    public function getVideoQuestion($courseId,$limit);
    public function getTypeWiseAllQuestion($type,$isVideo);

}
