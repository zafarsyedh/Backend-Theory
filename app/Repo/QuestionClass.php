<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionCourse;
use App\Models\QuestionSection;
use App\Models\QuestionSolved;
use App\Models\QuestionTranslation;
use App\Models\Role;
use App\Models\SolvedQuestions;
use App\Models\TopicArea;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use App\Traits\HandleFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());


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
            return  Helper::successWithData($data,(($id)?"Question Updated Successfully":"Question Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }


        }
   public function saveQuestionTranslation($request)
    {

        try {
            $id = $request->q_id;

            DB::beginTransaction();

            for ($c = 0; $c < count($request->t_lang); $c++) {

            $qTranslation = new QuestionTranslation();
            if (QuestionTranslation::where('q_id',$request->q_id)->where('lang',$request['t_lang'][$c])->first()) {
                $qTranslation = QuestionTranslation::where('q_id', $request->q_id)->where('lang',$request['t_lang'][$c])->first();
            }

            $qTranslation->q_id = $request->q_id;
            $qTranslation->lang_id = $request['t_lang_id'][$c];
            $qTranslation->q_title = $request['q_title'][$c]['title'];
            $qTranslation->opt_a = $request['opt_a'][$c]['title'];
            $qTranslation->opt_b = $request['opt_b'][$c]['title'];
            $qTranslation->opt_c = $request['opt_c'][$c]['title'];
                if($request['q_audio']){
                    foreach ($request['q_audio'][$c] as $key => $audioFile) {
                        if ($audioFile != null && $key == $request['t_lang'][$c]){
                            $this->qAudioname = $this->handleFiles($audioFile, $this->qAudioPath);
                            ($this->qAudioname != null) ? $qTranslation->q_audio = $this->qAudioname : "";
                        }
                    }
                }

                if($request['opt_a_audio']){
                    foreach ($request['opt_a_audio'][$c] as $key => $audioAFile) {
                        if ($audioAFile != null && $key == $request['t_lang'][$c] ){
                            $this->optAAudio = $this->handleFiles($audioAFile, $this->qAudioPath);
                            ($this->optAAudio != null) ? $qTranslation->opt_a_audio = $this->optAAudio : '';
                        }
                    }
                }

                if($request['opt_b_audio']){
                    foreach ($request['opt_b_audio'][$c] as $key => $audioBFile) {
                        if ($audioBFile != null && $key == $request['t_lang'][$c] ){
                            $this->optBAudio = $this->handleFiles($audioBFile, $this->qAudioPath);
                            ($this->optBAudio != null) ? $qTranslation->opt_b_audio = $this->optBAudio : '';
                        }
                    }
                }

                if($request['opt_c_audio']){
                    foreach ($request['opt_c_audio'][$c] as $key => $audioCFile) {
                        if ($audioCFile != null && $key == $request['t_lang'][$c] ){
                            $this->optCAudio = $this->handleFiles($audioCFile, $this->qAudioPath);
                            ($this->optCAudio != null) ? $qTranslation->opt_c_audio = $this->optCAudio : '';
                        }
                    }
                }

            $qTranslation->lang = $request['t_lang'][$c];
            $qTranslation->save();

            }
            DB::commit();
            $data=QuestionTranslation::where('q_id',$id)->get();
            return  Helper::successWithData($data,(($id)?"Question Translation Updated Successfully":"Question Translation Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }


        }
    public function getAllQuestionForAdminSide()
    {
        try {
            $qry=Question::with('questionDetail','topic.topicAreaTranslation','qCourses.course');
            $qry=$qry->orderBy('id','ASC');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function deleteQuestion($id)
    {
        try {
            DB::beginTransaction();
            $role = Question::find($id);
            $role->delete();
            $qtr=QuestionTranslation::where('q_id',$id)->delete();
            DB::commit();
            return Helper::successWithData($role, $message="Question Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function findQuestionById($id)
    {
        try {
            $res = QuestionTranslation::with('questions.qCourses.course.courseTranslation:id,course_id,full_name');
            $res = $res->where('q_id',$id);
            $res = $res->first();
            return  Helper::successWithData($res,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getQuestionTranslationsById($id)
    {
        try {
            $res = Question::with('questionTranslations','qCourses.course.courseTranslation:id,course_id,full_name');
            $res = $res->where('id',$id);
            $res = $res->first();

            return  Helper::successWithData($res,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }



    public function createNewAttempt($request)
    {
        try {
         $attempt=Attempt::where('std_id',$request->std_id)->where('status',0)->latest('id')->first();
                if(!$attempt){
                    $attempt = new Attempt();
                }
                $attempt->std_id= $request->std_id;
                $attempt->exam_type= $request->exam_type;
                $attempt->save();
                return $attempt->id;

        }catch (\Exception $e) {
            throw $e;
        }
    }
    public function questionMoveInSolvedQuestionTable($request)
    {
        try {
            DB::beginTransaction();
            $courseId=1;
            $qLang='en';
            $audioLang='en';

            $attemptId= $this->createNewAttempt($request);

            if(QuestionSolved::where('attempt_id',$attemptId)->where('is_answered',0)->count() > 0 ){
                return  Helper::successWithData($attemptId,'Record created');
            }


            $qry = Question::query();
            $qry = $qry->select('id');
            $qry = $qry->where(function ($query) use ($courseId) {
                $query->whereRelation('qCourses','course_id',$courseId)
                    ->orWhere('q_type','2');
            });
            $qry = $qry->where('status', 1);
            $qry=$qry->whereHas('questionTranslations', function($query) use($qLang)
            {
                $query->where('lang',$qLang);
            });


            $qry=$qry->limit(10);
            $qry=$qry->inRandomOrder();
              $allQuestion = $qry->get();
              if($allQuestion->count() ==0){
                  return  Helper::errorWithData('Record not found',$allQuestion);
              }

            foreach($allQuestion as $row){
                $question=QuestionSolved::updateOrCreate(
                    [
                        'q_id'=> $row->id,
                        'attempt_id'=>$attemptId,
                    ],
                    [

                        'attempt_id' =>$attemptId,
                        'q_id'=> $row->id,
                        'is_answered' =>0,
                        'q_lang'=>$qLang,
                        'audio_lang'=>$audioLang,
                    ]
                );
            }
            DB::commit();

            return  Helper::successWithData($question->attempt_id,'Record created');

        }
        catch (\Exception $e) {
            DB::rollBack();
            return  Helper::error($e->getMessage(),$e);
        }
    }
    public function getMovedQuestionForTheoryPractice($request,$attemptId,$purpose=1)
    {
        try {
         //   $purpose 2 for result and 1 for get question for exam

            $qLang=$request->q_lang;
            $audioLang=$request->audio_lang;

            $qry=QuestionSolved::query();
            $qry=$qry->with(['question.questionTranslations' => function ($query) use ($qLang) {
                $query->where('lang',$qLang);
            },'question.qLangAudio'=>  function ($query) use ($audioLang) {
                return $query->where('lang',$audioLang);
            },'question']);

            $qry=$qry->select('id','q_id','attempt_id','choosed_option','is_correct_ans','is_answered');
            $qry=$qry->where('attempt_id',$attemptId);
            ($purpose==1)? $qry= $qry->where('is_answered',0):'';
            return   $qry=$qry->get();

            return Helper::success($qry,'record found');
        }catch (\Exception $e) {
            return  $e->getMessage();
            return Helper::errorWithData($e->getMessage(), []);
        }
    }


}
