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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseClass implements CourseInterface
{

    public function getAllCourses()
    {
        try {

            $qry = Course::with('courseTranslation');

               $qry=$qry->with(['courseQuestions' => function ($query) {
                    $query->whereHas('question', function ($q) {
                        $q->where('q_is_video', 0);
                    });
                }, 'courseQuestions.question' => function ($query) {
                    $query->where('q_is_video', 0);
                }]);
              $qry=$qry->withCount(['courseQuestions as nonVideoQuestion' => function ($query) {
                    $query->whereHas('question', function ($q) {
                        $q->where('q_is_video', 0);
                    });
                }]);
              $qry=$qry->with(['courseQuestions' => function ($query) {
                  $query->whereHas('question', function ($q) {
                      $q->where('q_is_video', 1);
                  });
              }, 'courseQuestions.question' => function ($query) {
                  $query->where('q_is_video', 1);
              }]);
              $qry=$qry->withCount(['courseQuestions as videoQuestion' => function ($query) {
                  $query->whereHas('question', function ($q) {
                      $q->where('q_is_video', 1);
                  });
              }]);
              $qry=$qry->get();

            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getCourseConfig($id)
    {
        try {
            $qry=Course::with('courseConfig')->find($id);
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }
    public function getAllCourseTranslations()
    {
        try {
            $qry = CourseTranslation::with("course");
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }
    public function getAllCourseForDropdown()
    {
        try {
            $qry=Course::with('courseTranslation');
            $qry=$qry->where('status',1);
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function saveCourse($request)
    {
        try {

            $id=$request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'short_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('courses')->whereNull('deleted_at') . $id,
                ],
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $course = Course::updateOrCreate(
                [
                    'id' => $request->id,
                ],

                [
                    'short_name' =>$request->short_name,
                    'status' => $request->status,
                ]
            );


                $role = CourseTranslation::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'lang' => 'en',
                    ],

                    [
                        'full_name' =>$request->full_name,
                        'course_id' => $course->id,
                        'lang' => 'en',
                    ]
                );

            DB::commit();
            $data = Course::with("courseTranslation")->find($course->id);
            return  Helper::successWithData($data,(($id)?"Course Updated Successfully":"Course  Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), $e);
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
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

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
                        'instructions' => $request['instructions'][$c],
                        'video_link' => $request['video_link'][$c],
                    ]
                );

            }
            DB::commit();
            $data = Course::with("courseTranslation")->find($id);
            return  Helper::successWithData($data,(($id)?"Course Translation Updated Successfully":"Course Translation  Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), $e);
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
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());


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
                        'practice_duration' => $request->practice_duration,
                        'video_duration' => $request->video_duration,
                    ]
                );


            DB::commit();
            $data = Course::with("courseConfig")->find($id);
            return  Helper::successWithData($data,(($id)?"Course Configuration Updated Successfully":"Course Configuration  Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }
    public function deleteCourse($id)
    {
        try {
            DB::beginTransaction();
            $course =Course::find($id);
            $course->delete();
            $qtr=CourseTranslation::where('course_id',$id)->delete();
            DB::commit();
            return Helper::successWithData($course, $message="Course Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getCourseInfoByShortName($courseShortName)
    {
        try {
            $course= Course::with('courseConfig','courseTranslation')->where('short_name',$courseShortName)->first();
            return $course;
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }


}
