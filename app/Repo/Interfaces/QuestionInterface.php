<?php
namespace App\Repo\Interfaces;

interface QuestionInterface{

    public function getAllQuestion();

    //getInvigilator

    public function createQuestions($request);
    public function saveQuestionTranslation($request);
    public function deleteQuestion($id);
    public function editQuestion($id,$limit);
    public function findQuestionById($id);
    public function getQuestionTranslationsById($id);
    public function updateQuestion($request);

    public function getQuestionForPractice($request);
    public function savePracticeExam($request);
    public function countQuestionAcordingCourseAndType($courseId,$qType);

    public function getQuestionForActualExam($request,$exam);

    //saveActualExam
    public function saveActualExam($request);
    public function createVideoQuestions($request);
    public function getVideoQuestion($request,$exam);
    public function getQuestionsForExams($request,$exam);
    public function saveSolvedQuestionsForExam($request);

    public function getAllQuestionForAdminSide();
    public function saveSolvedQuestionsOfActualExam($request);
    public function countResult();
    public function getAllQuestions();
    public function getQuestionInfo($qId);




}
