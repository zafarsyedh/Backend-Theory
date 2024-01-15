<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Course;
use App\Models\CourseConfigration;
use App\Models\CourseTranslation;
use App\Models\Language;
use App\Repo\Interfaces\CourseInterface;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseClass implements CourseInterface
{

    public function getAllCourses()
    {
        $qry = Course::with("courseTranslation");
        $qry=$qry->get();
        return $qry;

    }
    public function getCourseConfig($id)
    {
//        $qry = CourseConfigration::with("course");
        $qry=Course::with('courseConfig')->find($id);
        return $qry;

    }
    public function getAllCourseTranslations()
    {
        $qry = CourseTranslation::with("course");
        $qry=$qry->get();
        return $qry;

    }
    public function getAllCourseForDropdown()
    {
        $qry=Course::with('courseTranslation');
        $qry=$qry->where('status',1);
        $qry=$qry->get();
        return $qry;

    }

    public function saveCourse($request)
    {
        if(Course::where('short_name',$request->short_name)->first()){
            return $response=[
                "status"=>"false",
                "messege"=>"This record already exist"
            ];
        }
        $course=new Course();
        $course->short_name = $request->short_name;
        $course->status=$request->status;
        if($course->save()){
            $courseTranslation=new CourseTranslation();
            $courseTranslation->course_id = $course->id;
            $courseTranslation->full_name = $request->full_name;
            $courseTranslation->lang = "en";
            if($courseTranslation->save()) {
                $data = Course::with("courseTranslation")->find($course->id);
                return $response = ([
                    "status" => "success",
                    "data" => $data,
                    "messege" => "Course Added Successfully"
                ]);
            }
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }
    }
    public function saveCourseTranslation($request)
    {

        try {

            $id=$request->course_id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'course_id' => 'required',
                'lang' => 'required',
            ]);
            if ($validator->fails())
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];


            for ($c = 0; $c < count($request['full_name']); $c++) {

                $role = CourseTranslation::updateOrCreate(
                    [
                        'course_id' => $request->course_id,
                        'lang' => $request['lang'][$c],
                    ],

                    [
                        'full_name' =>$request['full_name'][$c],
                        'course_id' => $request->course_id,
                        'lang' => $request['lang'][$c],
                    ]
                );

            }
            DB::commit();
            $data = Course::with("courseTranslation")->find($id);
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => "Course Translation Added Successfully"
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


    public function saveCourseConfig($request)
    {
        try {
            $id=$request->course_id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'course_id' => 'required',
            ]);
            if ($validator->fails())
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];

                $role = CourseConfigration::updateOrCreate(
                    [
                        'course_id' => $request->course_id,
                    ],

                    [
                        'specific_question' =>$request->specific_question,
                        'common_question' => $request->common_question,
                        'video_question' => $request->video_question,
                        'require_type' =>$request->require_type,
                        'specific_require' => $request->specific_require,
                        'common_require' => $request->common_require,
                        'video_require' => $request->video_require,
                        'total_require' => $request->total_require,
                        'total_duration' => $request->total_duration,
                        'video_duration' => $request->video_duration,
                    ]
                );


            DB::commit();
            $data = Course::with("courseConfig")->find($id);
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => "Course Configuration Added Successfully"
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


    public function deleteCourse($id)
    {
        $course =Course::find($id);
        $course->delete();
        return 1;

    }

    public function editCourse($id)
    {
        // TODO: Implement editAddon() method.
        return $addon = Language::find($id);
    }

    public function updateCourse($request)
    {
        try{
        $course=Course::find($request->id);
        $course->short_name = $request->short_name;
        $course->status=$request->status;
        if($course->save()){
            $courseTranslation= CourseTranslation::where('course_id',$course->id)->where("lang",'en')->first();
            $courseTranslation->course_id = $course->id;
            $courseTranslation->full_name = $request->full_name;
            $courseTranslation->lang = "en";
            if($courseTranslation->save()) {
                $data = Course::with("courseTranslation")->find($course->id);
//                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = "Course Updated Successfully", $data );
                return $response = ([
                    "status" => "success",
                    "data" => $data,
                    "messege" => "Course Updated Successfully"
                ]);
            }
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];
        }
        } catch (\Exception $e) {
            return $response = Helper::sendError($e->getMessage(),$errors= [], $code = 400);
        }
    }










}
