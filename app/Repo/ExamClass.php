<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Category;
use App\Models\Exam;
use Illuminate\Support\Facades\Request;

class ExamClass implements Interfaces\ExamInterface
{
    public function getQuestionForExam(Request $request)
    {
        try {
            $data['topics']=Helper::fetchOnlyData($this->topics->getAllTopics());
            return view('admin.q-bank.index')->with(compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
