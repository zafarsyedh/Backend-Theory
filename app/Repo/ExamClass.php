<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\QuestionSolved;


class ExamClass implements Interfaces\ExamInterface
{

    public function saveExamQuestion($request)
    {
        try {
            if(count($request->markedQuestions) > 0) {
                foreach ($request->markedQuestions as $q) {

                    $examQ = QuestionSolved::find($q['id']);
                    $examQ->choosed_option = $q['selectedValue'];
                    $examQ->is_correct_ans = ($q['correctAns'] == $q['selectedValue']) ? 1 : 0;
                    $examQ->is_answered = 1;
                    $examQ->save();

                }
            }
            return Helper::successWithData([],'record save');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }

    public function savePracticeQuestion($request)
    {
        try {
                $examQ= QuestionSolved::find($request->id);
                $examQ->choosed_option=$request->selectedOpt;
                $examQ->is_correct_ans=($request->selectedOpt==$request->correctOpt)?1:0;
                $examQ->is_answered=1;
                $examQ->save();
            return Helper::successWithData([],'record save');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }
}
