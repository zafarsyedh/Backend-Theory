<?php
namespace App\Repo\Interfaces;

interface QuestionInterface{




    public function createQuestions($request);
    public function saveQuestionTranslation($request);
    public function deleteQuestion($id);

    public function findQuestionById($id);
    public function getQuestionTranslationsById($id);



    public function createNewAttempt($request);

    public function questionMoveInSolvedQuestionTable($request);





}
