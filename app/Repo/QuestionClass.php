<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionCourse;
use App\Models\QuestionTranslation;
use App\Models\Role;
use App\Models\SolvedQuestions;
use App\Models\TopicArea;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use App\Traits\HandleFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function PHPUnit\Framework\exactly;
use function React\Promise\all;

class QuestionClass implements Interfaces\QuestionInterface
{
use HandleFiles;

protected $qImagePath='q-images/';
protected $qImageName='';
protected $qAudioname='';
    protected $optAImage='';
    protected $optBImage='';
    protected $optCImage='';
    protected $optAAudio='';
    protected $optBAudio='';
    protected $optCAudio='';
    protected $qAudioPath='q-audios/';
    protected $qVideoPath='q-video/';
    protected $qVideo='';
    protected $lang_id='';
    protected $qLangId='';
    protected $attempt=0;

    use HandleFiles;
    public function createQuestions($request)
    {

        try {
            $id = $request->question_edit_id;

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'q_type' => 'required',
                'topic_id' => 'required',
                'q_title' => 'required',
            ]);
            if ($request->q_type == 'specific') {
                $validator = Validator::make($request->all(), [
                    'course_id' => 'required',
                ]);
            }

            if ($validator->fails())
                $response =Helper::vaidationError($status = 'false', $errors = $validator->errors(), $message = $validator->errors()->first());


            if ($file = $request->file('q_image')) {
                $this->qImageName = $this->handleFiles($file, $this->qImagePath);
            }

            if ($file = $request->file('q_audio')) {
                $this->qAudioname = $this->handleFiles($file, $this->qAudioPath);
            }

            if ($file = $request->file('q_video')) {
//            if ($request->video_id) {

//                $this->qVideo = $request->video_id;
                $this->qVideo = $this->handleFiles($file, $this->qVideoPath);
            }


            if ($file = $request->file('opt_a_image')) {
                $this->optAImage = $this->handleFiles($file, $this->qImagePath);
            }

            if ($file = $request->file('opt_b_image')) {
                $this->optBImage = $this->handleFiles($file, $this->qImagePath);
            }

            if ($file = $request->file('opt_c_image')) {
                $this->optCImage = $this->handleFiles($file, $this->qImagePath);
            }

            if ($file = $request->file('opt_a_audio')) {
                $this->optAAudio = $this->handleFiles($file, $this->qAudioPath);
            }

            if ($file = $request->file('opt_b_audio')) {
                $this->optBAudio = $this->handleFiles($file, $this->qAudioPath);
            }
            if ($file = $request->file('opt_c_audio')) {
                $this->optCAudio = $this->handleFiles($file, $this->qAudioPath);
            }

            $question = new Question();
            if (Question::find($id)) {
                $question = Question::find($id);
            }

            $question->code=$request->q_code;
            $question->q_type=$request->q_type;
            $question->q_is_video=$request->is_video;
            $question->course_id=1;
            $question->topic_id=$request->topic_id;
            $question->correct_opt=$request->correct_opt;


            ($this->optAImage != null) ? $question->opt_a_image = $this->optAImage : '';
            ($this->optBImage != null) ? $question->opt_b_image = $this->optBImage : '';
            ($this->optCImage != null) ? $question->opt_c_image = $this->optCImage : '';
            ($this->qImageName != null) ? $question->q_image = $this->qImageName : '';
            ($this->qVideo != null) ? $question->q_video = $this->qVideo : '';
            $question->save();


            if($request->q_type == 'specific') {
                $data=$request->course_id;
                QuestionCourse::where('q_id',$question->id)->delete();
                for ($c = 0; $c < count($data); $c++) {
                    $questionCourse = QuestionCourse::updateOrCreate(
                        [
                            'q_id' => $question->id,
                            'course_id' => $data[$c],
                        ],
                        [
                            'q_id' =>$question->id,
                            'course_id' => $data[$c],
                        ]
                    );
                }
            }

            $qTranslation = new QuestionTranslation();
            if (QuestionTranslation::where('q_id',$question->id)->where('lang','en')->first()) {
                $qTranslation = QuestionTranslation::where('q_id', $question->id)->where('lang','en')->first();
            }


            $qTranslation->q_id = $question->id;
            $qTranslation->lang_id = $request->lang_id;
            $qTranslation->q_title = $request->q_title;
            $qTranslation->opt_a = $request->opt_a;
            $qTranslation->opt_b = $request->opt_b;
            $qTranslation->opt_c = $request->opt_c;
            ($this->optAAudio != null) ? $qTranslation->opt_a_audio = $this->optAAudio : '';
            ($this->optBAudio != null) ? $qTranslation->opt_b_audio = $this->optBAudio : '';
            ($this->optCAudio != null) ? $qTranslation->opt_c_audio = $this->optCAudio : '';
            ($this->qAudioname != null) ? $qTranslation->q_audio = $this->qAudioname : '';
            $qTranslation->lang = 'en';
            $qTranslation->save();


            DB::commit();
            $data=Question::with('questionDetail','course.courseTranslation','topic.topicAreaTranslation')->find($question->id);
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => (($id)?"Question Updated Successfully":"Question Added Successfully")
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $validationException->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $e->getMessage()
            ];
        }


        }
        public function saveQuestionTranslation($request)
    {

        try {
            $id = $request->q_id;




            DB::beginTransaction();


            if ($file = $request->file($request['opt_a_audio'][0]['title'])){
            $this->optAAudio = $this->handleFiles($file, $this->qAudioPath);
        }
            return $response = ([
                "status" => "success",
                "data" =>$this->optAAudio,
                "messege" => (($id)?"Question Translation Updated Successfully":"Question Translation Added Successfully")
            ]);



            for ($c = 0; $c < count($request->t_lang); $c++) {


                if ($audio = $request->opt_a_audio) {
                    $file = $request->file('opt_a_audio');
                    $this->qAudioname = $this->handleFiles($file, $this->qAudioPath);
                }

//                if ($file = $request->file('opt_a_audio')) {
//                    $this->optAAudio = $this->handleFiles($file, $this->qAudioPath);
//                }
//
//                if ($file = $request->file('opt_b_audio')) {
//                    $this->optBAudio = $this->handleFiles($file, $this->qAudioPath);
//                }
//                if ($file = $request->file('opt_c_audio')) {
//                    $this->optCAudio = $this->handleFiles($file, $this->qAudioPath);
//                }


                $role = QuestionTranslation::updateOrCreate(
                    [
                        'q_id' => $request->q_id,
                        'lang' => $request['t_lang'][$c],
                    ],

                    [
                        'q_id' =>$request->q_id,
                        'lang_id' =>$request['t_lang_id'][$c],
                        'q_title' =>$request['q_title'][$c]['title'],
                        'q_audio' => 1,
//                        'q_audio' => 1,
                        'opt_a' =>$request['opt_a'][$c]['title'],
                        'opt_b' =>$request['opt_b'][$c]['title'],
                        'opt_c' =>$request['opt_c'][$c]['title'],
//                        'opt_a_audio' =>$request['opt_a_audio'][$c]['title'],
//                        'opt_b_audio' =>$request['opt_b_audio'][$c]['title'],
//                        'opt_c_audio' =>$request['opt_c_audio'][$c]['title'],
                        'opt_a_audio' => $this->qAudioname,
                        'opt_b_audio' =>1,
                        'opt_c_audio' =>1,
                        'lang' => $request['t_lang'][$c],
                    ]
                );

            }






//            $qTranslation->q_id = $question->id;
//            $qTranslation->lang_id = $request->lang_id;
//            $qTranslation->q_title = $request->q_title;
//            $qTranslation->opt_a = $request->opt_a;
//            $qTranslation->opt_b = $request->opt_b;
//            $qTranslation->opt_c = $request->opt_c;
//            ($this->optAAudio != null) ? $qTranslation->opt_a_audio = $this->optAAudio : '';
//            ($this->optBAudio != null) ? $qTranslation->opt_b_audio = $this->optBAudio : '';
//            ($this->optCAudio != null) ? $qTranslation->opt_c_audio = $this->optCAudio : '';
//            ($this->qAudioname != null) ? $qTranslation->q_audio = $this->qAudioname : '';
//            $qTranslation->lang = 'en';
//            $qTranslation->save();


            DB::commit();
            $data=QuestionTranslation::where('q_id',$id)->get();
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => (($id)?"Question Translation Updated Successfully":"Question Translation Added Successfully")
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $validationException->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $e->getMessage()
            ];
        }


        }
    public function getAllQuestion()
    {
        $qry=Question::with('questionDetail','course','topic');
        $qry=$qry->orderBy('id','ASC');
        $qry=$qry->get();
        return $qry;
    }

    public function getAllQuestionForAdminSide()
    {
        $qry=Question::with('questionDetail','course.courseTranslation','topic.topicAreaTranslation');
        $qry=$qry->orderBy('id','ASC');
        $qry=$qry->get();
        return $qry;
    }
    public function deleteQuestion($id)
    {
        try {
            $role = Question::find($id);
            $role->delete();
            $qtr=QuestionTranslation::where('q_id',$id)->delete();
            return Helper::success($role, $message = "Question Deleted");
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }
    public function editQuestion($id,$limit)
    {
          $qry = Language::with(['questionTranslation' => function ($query) use ($id) {
            $query->where('q_id',$id);
        }]);
        ($limit > 0)? $qry=$qry->limit(1):'';
        $qry=$qry->where('is_deleted',0);
        $qry=$qry->where('status',1);
        $qry=$qry->get();
        return $qry;


        $qry=Question::with('questionTranslation','questionTranslation.lang');
        $qry=$qry->where('id',$id)->first();
        return $qry;
    }
    public function findQuestionById($id)
    {
        try {
            $res = QuestionTranslation::with('questions.qCourses');
            $res = $res->where('q_id',$id);
            $res = $res->first();
            return Helper::success($res, $message = __('translation.record_found'));
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }
    public function getQuestionTranslationsById($id)
    {
        try {
            $res = Question::with('questionTranslations');
            $res = $res->where('id',$id);
            $res = $res->first();
            return Helper::success($res, $message = __('translation.record_found'));
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }
    public function updateQuestion($request)
    {
       $res=DB::transaction(function() use ($request) {
             $input = $request->all();

            if ($file = $request->file('q_image')) {
                $this->qImageName = $this->handleFiles($file, $this->qImagePath);
            }
            if ($file = $request->file('opt_a_image')) {
                $this->optAImage = $this->handleFiles($file, $this->qImagePath);
            }
            if ($file = $request->file('opt_b_image')) {
                $this->optBImage = $this->handleFiles($file, $this->qImagePath);
            }
            if ($file = $request->file('opt_c_image')) {
                $this->optCImage = $this->handleFiles($file, $this->qImagePath);
            }


            if ($file = $request->file('q_video')) {
                $this->qVideo = $this->handleFiles($file, $this->qVideoPath);
            }

            $q_id=$request->q_id;

             $q = Question::find($q_id);

            $q->course_id =$request->course_id;
            $q->q_type = $request->q_type;
            $q->correct_opt = $request->correct_opt;
            ($this->qImageName)?$q->q_image =$this->qImageName:'';
            ($this->optAImage)?$q->opt_a_image =$this->optAImage:'';
            ($this->optBImage)?$q->opt_b_image =$this->optBImage:'';
            ($this->optCImage)?$q->opt_c_image =$this->optCImage:'';

            ($this->qVideo)?$q->q_video =$this->qVideo:'';

            $q->save();

            for ($c = 0; $c < count($input['q_content']); $c++) {


                if ( $request->hasFile('q_audio.'.$c)) {
                    $audioFile = $request->file('q_audio.'.$c);
                    $this->qAudioname = $this->handleFiles($audioFile, $this->qAudioPath);
                }

                if ($file = $request->file('opt_a_audio.'.$c)) {
                    $this->optAAudio = $this->handleFiles($file, $this->qAudioPath);
                }
                if ($file = $request->file('opt_b_audio.'.$c)) {
                    $this->optBAudio = $this->handleFiles($file, $this->qAudioPath);
                }
                if ($file = $request->file('opt_c_audio.'.$c)) {
                    $this->optCAudio = $this->handleFiles($file, $this->qAudioPath);
                }

                $qTr=QuestionTranslation::find($input['id'][$c]);
                if(!$qTr){
                    $qTr=new QuestionTranslation();
                }
                $qTr->q_id = $q_id;
                $qTr->lang_id =$input['lang_id'][$c];
                $qTr->q_content =$input['q_content'][$c];
                $qTr->opt_a =$input['opt_a'][$c];
                $qTr->opt_b =$input['opt_b'][$c];
                $qTr->opt_c =$input['opt_c'][$c];
                ($this->qAudioname)?$qTr->q_audio =$this->qAudioname:'';
                ($this->optAAudio)?$qTr->opt_a_audio =$this->optAAudio:'';
                ($this->optBAudio)?$qTr->opt_b_audio =$this->optBAudio:'';
                ($this->optCAudio)?$qTr->opt_c_audio =$this->optCAudio:'';
                $qTr->save();


            }
        });
        return $response=1;
    }

    public function savePracticeExam($request)
    {

        $request->all();
        $this->attempt=$request->attempt;

        if ($request->ans !=2) {
            if($this->attempt ==0) {
                $chekAttempt = Attempt::where('std_id', $request->std_id)->latest('id')->first();
                $this->attempt=$chekAttempt->id;
            }

            if (SolvedQuestions::where('std_id', $request->std_id)->where('q_id', $request->q_id)->where('exam_type', 0)->where('attempt',$this->attempt)->first()) {
                return $response = 'question already solved';
            }

            $solQ = new SolvedQuestions();
            $solQ->std_id = $request->std_id;
            $solQ->q_id = $request->q_id;
            $solQ->ans = $request->ans;
            $solQ->attempt = ($this->attempt > 0) ? $this->attempt : 1;
            $solQ->q_type = ($request->q_type)?2:1;
            $solQ->exam_type = 0;
            $solQ->save();
            return $response = 'success';
        }
    }
    public function saveActualExam($request)
    {


            $data = $request->all();
            $std_id = $request->std_id;
            $data = $data['selectedOptions'];
            $qData = $request->qData;
            $exam_id = $request->examId;
            foreach ($data as $key => $val) {

                if ($val['qId']) {
                    $q = Question::find($val['qId']);
                    $solQ = new SolvedQuestions();
                    $solQ->std_id = $std_id;
                    $solQ->q_id = $val['qId'];
                    $solQ->ans = $q->correct_opt == $val['correctAns'] ? 1 : 0;
                    $solQ->exam_type = 1;
                    $solQ->choosedOption = $val['correctAns'];
                    $solQ->exam_id = $exam_id;
                    $solQ->save();

                }
            }
            foreach ($qData as $row) {
                if (!SolvedQuestions::where('q_id', $row['id'])->where('std_id', $std_id)->where('exam_type', 1)->first()) {

                    $solQ = new SolvedQuestions();
                    $solQ->std_id = $std_id;
                    $solQ->q_id = $row['id'];
                    $solQ->ans = 0;
                    $solQ->exam_type = 1;
                    $solQ->choosedOption = 0;
                    $solQ->exam_id = $exam_id;
                    $solQ->is_skipped = 1;
                    $solQ->save();
                }

            }

        return $response = 'success';
    }

    public function  saveSolvedQuestionsForExam($request)
    {
        $request->all();
        $this->attempt=$request->attempt;
        $examInfo = Exam::where('std_id', $request->std_id)->where('status',0)->latest('id')->first();

            if($request->exam_type==2 && $this->attempt ==0) {
                $chekAttempt = Attempt::where('std_id', $request->std_id)->latest('id')->first();
                $this->attempt=$chekAttempt->id;


            }

            if($request->exam_type==2) {
                if (SolvedQuestions::where('std_id', $request->std_id)->where('q_id', $request->q_id)->where('exam_type', $request->exam_type)->where('attempt', $this->attempt)->first()) {
                    return $response = 'question already solved';
                }
            }

        if($request->exam_type==1) {
            if (SolvedQuestions::where('std_id', $request->std_id)->where('q_id', $request->q_id)->where('exam_type', $request->exam_type)->first()) {
                return $response = 'question already solved';
            }
        }

            $solQ = new SolvedQuestions();
            $solQ->std_id = $request->std_id;
            $solQ->q_id = $request->q_id;
            $solQ->ans = $request->ans;
            $solQ->attempt = ($this->attempt > 0) ? $this->attempt : 1;
            $solQ->q_type = $request->q_type;
            $solQ->exam_type =$request->exam_type;
            $solQ->choosedOption =$request->choosedOption;
            ($request->exam_type==1)?$solQ->exam_id =$examInfo->id:'';
            $solQ->save();
            return $response = 'success';

    }

    public function  saveSolvedQuestionsOfActualExam($request)
    {


    }

    public function countQuestionAcordingCourseAndType($courseId,$type)
    {
        // TODO: Implement getotalQuestionAcordingCourse() method.
        $qry= Question::where('course_id',$courseId);
       ($type > 0)?$qry=$qry->where('q_type',$type):'';
        $qry=$qry->get();
        return $qry;
    }
    public function getQuestionForPractice($request)
    {
        $this->lang_id=$request->lang_id;
        $this->qLangId=$request->audioLangId;
        $std_id=$request->std_id;
        $this->attempt=$request->attempt;

        $exam=Exam::where('std_id',$std_id)->where('status',0)->first();
        $course_id=$exam->course_id;


        $qry = Question::Query();
        $qry=$qry->with(['qWithLang' => function ($query) {
            return $query->where('lang_id',$this->lang_id);
        },'qWithLang.lang','qLangAudio'=>  function ($query) {
            return $query->where('lang_id',$this->qLangId);
        }]);

        $qry=$qry->where('course_id',$course_id);

        $qry=$qry->where('q_type',1);
        ($this->attempt > 0)? $qry=$qry->whereNotIn('id', function ($query) {
            $query->select('q_id')->from('solved_questions')->where('attempt',$this->attempt);
        }):'';


        $qry=$qry->get();
        return $qry;


    }
    public function getQuestionForActualExam($request,$exam)
    {

        $this->lang_id=$request->lang_id;
        $this->qLangId=$request->audioLangId;

         $course_id=$exam->course_id;
         $questionLimit=$exam->config->total_question;

        $qry = Question::Query();
        $qry = $qry->select('questions.*');
        $qry=$qry->join('question_translations', 'question_translations.q_id', 'questions.id');
        $qry=$qry->where('question_translations.lang_id',$this->lang_id);

           $qry=$qry->with(['qWithLang' => function ($query) {
            return $query->where('lang_id',$this->lang_id);
        },'qLangAudio'=>  function ($query) {
            return $query->where('lang_id',$this->qLangId);
        }]);

        $qry=$qry->where('q_type',1);
        $qry=$qry->where('course_id',$course_id);
        $qry=$qry->inRandomOrder()->take($questionLimit);
        $qry=$qry->get();
        return $qry;




            //actual old  query
//        $qry = Question::Query();
//        $qry=$qry->with(['qWithLang' => function ($query) {
//            return $query->where('lang_id',$this->lang_id);
//        },'qWithLang.lang','qLangAudio'=>  function ($query) {
//            return $query->where('lang_id',$this->qLangId);
//        }]);
//
//        $qry=$qry->where('q_type',1);
//        $qry=$qry->where('course_id',$course_id);
//        $qry=$qry->inRandomOrder()->take($questionLimit);
//        $qry=$qry->groupBy('q_type');
//        $qry=$qry->get();
//        return $qry;
    }
    public  function  getQuestionsForExams($request,$exam){

         $request->all();

        $this->lang_id=$request->lang_id;
        $this->qLangId=$request->audioLangId;

        $course_id=$exam->course_id;
        $questionLimit=$exam->config->total_question;
        $this->attempt=$request->attempt;

        $qry = Question::Query();
        $qry = $qry->select('questions.*');
        $qry=$qry->join('question_translations', 'question_translations.q_id', 'questions.id');
        ($request->q_type!=2)?$qry=$qry->where('question_translations.lang_id',$this->lang_id):'';

     if($request->q_type==1){
            $qry=$qry->with(['qWithLang' => function ($query) {
            return $query->where('lang_id',$this->lang_id);
        },'qLangAudio'=>  function ($query) {
            return $query->where('lang_id',$this->qLangId);
        }]);
         $qry = $qry->where('q_type',$request->q_type);
        }

        ($request->q_type==2)?$qry=$qry->with('qWithLang','qLangAudio'):'';

        $qry=$qry->where('course_id',$course_id);

//        exam type 2 use for practice test
        if($request->testType==2) {
            $qry = $qry->where('q_type',$request->q_type);
            ($this->attempt > 0) ? $qry = $qry->whereNotIn('questions.id', function ($query) {
                $query->select('q_id')->from('solved_questions')->where('attempt',$this->attempt);
            }) : '';
        }

        ($request->testType==1)?$qry=$qry->inRandomOrder()->take($questionLimit):'';
        ($request->testType==2)?$qry=$qry->inRandomOrder():'';
        $qry=$qry->get();
        return $qry;
    }

    public function createVideoQuestions($request)
    {
        $res=DB::transaction(function() use ($request) {
            $input = $request->all();


            if ($file = $request->file('qVideo')) {
                $this->qVideo = $this->handleFiles($file, $this->qVideoPath);
            }

            if ($file = $request->file('optAImage')) {
                $this->optAImage = $this->handleFiles($file, $this->qImagePath);
            }
            if ($file = $request->file('optBImage')) {
                $this->optBImage = $this->handleFiles($file, $this->qImagePath);
            }
            if ($file = $request->file('optCImage')) {
                $this->optCImage = $this->handleFiles($file, $this->qImagePath);
            }

            $q = new Question();
            $q->course_id = $request->course_id;
            $q->q_type = 2;//$request->q_type;
            $q->q_video = $this->qVideo;
            $q->correct_opt = $request->correct_opt;
            $q->q_image =$this->qImageName;
            $q->opt_a_image =$this->optAImage;
            $q->opt_b_image =$this->optBImage;
            $q->opt_c_image =$this->optCImage;
            $q->save();


                if ($file = $request->file('optAAudio')) {
                    $this->optAAudio = $this->handleFiles($file, $this->qAudioPath);
                }
                 if ($file = $request->file('optBAudio')) {
                    $this->optBAudio = $this->handleFiles($file, $this->qAudioPath);
                }
                    if ($file = $request->file('optCAudio')) {
                    $this->optCAudio = $this->handleFiles($file, $this->qAudioPath);
                }


                $qTr= new QuestionTranslation();
                $qTr->q_id =$q->id;
                $qTr->lang_id =1;
                $qTr->q_content =$request->vidQContent;
                $qTr->opt_a =($input['optA']!='undefined')?$input['optA']:'';
                $qTr->opt_b =($input['optB']!='undefined')?$input['optB']:'';
                $qTr->opt_c =($input['optB']!='undefined')?$input['optB']:'';
                $qTr->opt_a_audio =$this->optAAudio;
                $qTr->opt_b_audio =$this->optBAudio;
                $qTr->opt_c_audio =$this->optCAudio;
                $qTr->save();


        });
        return $response='success';
    }

    public function getVideoQuestion($request,$exam)
    {


          $course_id=$exam->course_id;
          $questionLimit=$exam->config->video_question;
          $this->attempt=$request->attempt;

        $qry = Question::Query();
       $qry=$qry->with('qWithLang','qLangAudio');
       $qry=$qry->where('course_id',$course_id);
       $qry=$qry->where('q_type',2);
       $qry=$qry->inRandomOrder()->take($questionLimit);
        $qry=$qry->get();
        return $qry;
    }



    public function countResult()
    {
        return  $qry=SolvedQuestions::where('exam_type',1)->distinct()->count('exam_id');
    }

    public function getAllQuestions()
    {
        return  $qry=Question::get();
    }

    public function getQuestionInfo($qId)
    {
        // TODO: Implement getQuestionInfo() method.
        return Question::find($qId);
    }


}
