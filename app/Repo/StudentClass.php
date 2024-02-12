<?php

namespace App\Repo;
use App\Events\CourseEvent;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Language;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\System;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use App\Repositries\course\CourseRepositry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use mysql_xdevapi\Exception;

class StudentClass implements Interfaces\StudentInterface
{


    //saveStudent

    public function saveStudent($request)
    {
        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'exam_type' => 'required',
                'q_lang' => 'required',
                'audio_lang' => 'required',
                'system_id' => 'required',
                'stdData' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

             $stdData=$request->stdData;
            $course=new CourseClass();
            if(!$courseInfo=$course->getCourseInfoByShortName($stdData['appltype'])){
                return Helper::error('Student Course not exist',[]);
            }

            $student = Student::updateOrCreate(
                [
                    'traffic_id' =>$stdData['regnnumb'],
                ],
                [
                    'traffic_id' =>$stdData['regnnumb'],
                    'std_name' => $stdData['studname'],
                    'email' => $stdData['email'],
                    'password' => '123456',
                    'std_gender' => $stdData['std_gender'],
                    'geartype' => $stdData['geartype'],
                    'language' => $stdData['language'],
                    'branch' => $stdData['branch'],
                    'mobile_no' => $stdData['mobileno'],
                    'progress' => $stdData['progress'],
                    'brcode' => $stdData['brcode'],
                    'coursetype' => $stdData['coursetype'],
                    'prefferd_golden_chance' => $stdData['prefferd_golden_chance'],
                    'historycls' => $stdData['historycls'],
                    'pendingamount' => $stdData['paidamount'],
                    'paidamount' => $stdData['paidamount'],

                ]
            );

            $stdCourse = StudentCourse::updateOrCreate(
                [
                    'std_id' => $student->id,
                    'course_id' => $courseInfo->id,
                ],
                [
                    'std_id' =>$student->id,
                    'course_id' => $courseInfo->id,
                    'is_active' =>1,
                ]
            );

            StudentCourse::where('std_id',$student->id)->update(['is_active'=>0]);
            StudentCourse::where('std_id',$student->id)->where('course_id',$stdCourse->course_id)->update(['is_active'=>1]);

            $paramData = [
                'stdId' =>$student->id,
                'courseId' =>$courseInfo->id,
                'invgId' =>1,
            ];

           $qLangInfo= Language::where('lang_short',$request->q_lang)->latest('id')->first();
           $audioLangInfo= Language::where('lang_short',$request->audio_lang)->latest('id')->first();
           $systemInfo= System::find($request->system_id);

            $eventStdData = [
                'stdId' =>$student->id,
                'stdName' =>$student->std_name,
                'trafficId' =>$student->traffic_id,
                'courseId' =>$courseInfo->id,
                'courseName' =>$courseInfo->short_name,
                'qLangShortName' =>$request->q_lang,
                'qLangFullName' =>$qLangInfo->lang,
                'audioLangShortName' =>$request->audio_lang,
                'audioLangFullName' =>$audioLangInfo->lang,
                'examType' =>$request->exam_type,
                'direction' =>($qLangInfo->direction == 2)? 'ltr':'rtl',
                'systemIp' =>$systemInfo->system_ip,

            ];

           Helper::createExamHelper($request,$paramData);
            event(new CourseEvent($eventStdData));

            DB::commit();

            return  Helper::successWithData($student,'Record created successfully');
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),[]);
        }
    }


}
