<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Category;
use App\Models\CourseConfigration;
use App\Models\Result;

class CategoryClass implements Interfaces\CategoryInterface
{

    public function getAllCategory()
    {
        $qry=Category::query();
        $qry=$qry->where('is_deleted',0)->orderBy('id','DESC');
        $qry=$qry->paginate(10);
        return $qry;

    }

    public function getCourseDropdown()
    {
        $qry=Category::query();
        $qry=$qry->where('status',1);
        $qry=$qry->where('is_deleted',0)->orderBy('id','DESC');
        $qry=$qry->get();
        return $qry;

    }

    public function saveCategory($request)
    {
        // TODO: Implement saveAddon() method.
        if(Category::where('cat_title',$request->title)->where('is_deleted',0)->first()){
            return $response='This record already exist';
        }
        $category=new Category();
        $category->course_code=$request->code;
        $category->cat_title=$request->title;
        $category->status=$request->status;
        if($category->save()){
            return $response='success';
        }else{
            return $response='Record not save due to some technical error';
        }
    }

    public function deleteCategory($id)
    {
        // TODO: Implement deleteAddon() method.
        $addon =Category::find($id);
        $addon->is_deleted=1;
        $addon->save();
        return 1;
    }

    public function editCategory($id)
    {
        // TODO: Implement editAddon() method.
        return $category = Category::find($id);
    }

    public function updateCategory($request)
    {
        // TODO: Implement updateAddon() method.
        $category=Category::find($request->id);
        $category->course_code=$request->code;
        $category->cat_title=$request->title;
        $category->status=$request->status;
        $category->save();
        return 1;
    }

    public function getSpecificCourseIdOnTheBaseOfName($courseName)
    {
        // TODO: Implement getSpecificCourse() method.
        return $category = Category::where('course_code',$courseName)->first();
    }

    public function saveCourseConfig($request)
    {
        // TODO: Implement saveAddon() method.
        try{

            $config=CourseConfigration::where('course_id',$request->courseId)->first();
            if(!$config){
                $config=new CourseConfigration();
            }

            $config->course_id =$request->courseId;
            $config->total_question=$request->totalQuestion;
            $config->correct_ans=$request->correctAns;
            $config->standard_time=$request->testTime;
            $config->video_time=$request->videoTime;
            $config->video_question=$request->videoTotalQuestion;
            $config->video_correct=$request->videoCorrect;
            if($config->save()){
                return $response='success';
            }else{
                return $response='Record not save due to some technical error';
            }
        }catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }


    }

    public function getCourseConfigInfoAndQuestions()
    {
        $qry=Category::with('courseConfig');
        $qry=$qry->where('status',1);
        $qry=$qry->where('is_deleted',0);
        $qry=$qry->inRandomOrder()->first();
        return $qry;
    }

    public function getCourseConfigInfo($id)
    {
        // TODO: Implement editAddon() method.
        return $category = CourseConfigration::where('course_id',$id)->first();
    }




}
