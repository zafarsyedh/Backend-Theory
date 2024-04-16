<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Course;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionCourse;
use App\Models\QuestionTranslation;
use App\Models\TopicArea;
use App\Models\TopicAreaTranslation;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\LanguageInterface;
use App\Repo\Interfaces\QuestionInterface;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Hash;

class QuestionImport implements ToModel, WithHeadingRow
{
    protected $qImagePath='q-images/';
    protected $qAudioPath='q-audios/';
    public function model(array $row)
    {



        if ($row['question']) {

            $lang=Language::where('lang',$row['language'])->first();
            if($row['language'] == 'English') {
                $courseId=NULL;
                if(!$topicTrans=TopicAreaTranslation::where('full_name',$row['topic_area'])->first()){
                    $topic=new TopicArea();
                    $topic->status=1;
                    $topic->save();

                     TopicAreaTranslation::upsert([
                         ['full_name' => $row['topic_area'],'topic_area_id'=>$topic->id,'lang'=>'en'],
                     ], ['full_name']);
                     $topicId=$topic->id;
                }else{
                     $topicId=$topicTrans->topic_area_id;
                }
                 $q= Question::updateOrCreate(
                     [
                         'code' => $row['item_id'],
                     ],
                     [
                         'code' => $row['item_id'],
                         'q_type' => $row['question_type'],
                         'course_id' => 1,
                         'topic_id' => $topicId,
                         'correct_opt' => strtolower($row['answer']),
                         'q_image' => ($row['photo'])? $this->qImagePath . $row['photo']:'',
                         'opt_a_image' => ($row['option_a_photo'])? $this->qImagePath . $row['option_a_photo']:'',
                         'opt_b_image' => ($row['option_b_photo'])? $this->qImagePath . $row['option_b_photo']:'',
                         'opt_c_image' => ($row['option_c_photo'])? $this->qImagePath . $row['option_c_photo']:'',
                         'q_is_video' => 0,
                     ]
                 );

                 if($row['question_type']=='specific') {

                     $explodedCourses = explode(',', $row['course']);

                     foreach ($explodedCourses as $course) {
                      $course = Course::where('short_name',$course)->latest('id')->first();

                         QuestionCourse::where('q_id',$q->id,)->delete();
                      $questionCourse = QuestionCourse::updateOrCreate(
                             [
                                 'q_id' =>$q->id,
                                 'course_id' =>$course->id,
                             ],
                             [
                                 'q_id' =>$q->id,
                                 'course_id' =>$course->id,
                             ]
                         );
                     }

                 }
            }
             else{
                 $q= Question::where('code',$row['item_id'])->latest('id')->first();
             }
             if ($lang) {
                 $qTr= QuestionTranslation::updateOrCreate(
                     [
                         'q_id' =>$q->id,
                         'lang_id' =>$lang->id,
                     ],
                     [
                         'q_id' => ($q)?$q->id:1,
                         'lang_id' =>($lang) ? $lang->id : 0,
                         'q_title' => $row['question'],
                         'opt_a' => $row['option_a']!=''?$row['option_a']:' ' ,
                         'opt_b' => $row['option_b']!=''?$row['option_b']:' ',
                         'opt_c' => $row['option_c']!=''?$row['option_c']:' ',
                         'q_audio' => ($row['audio_url'])?  $this->qAudioPath . $row['audio_url']:'',
                         'opt_a_audio' =>($row['option_a_audio'])? $this->qAudioPath . $row['option_a_audio']:'',
                         'opt_b_audio' =>($row['option_b_audio'])? $this->qAudioPath . $row['option_b_audio']:'',
                         'opt_c_audio' =>($row['option_c_audio'])?  $this->qAudioPath . $row['option_c_audio']:'',
                         'lang' =>$lang->lang_short,
                     ]
                 );

             }
        }




    }

}
