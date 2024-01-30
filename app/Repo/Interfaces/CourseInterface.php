<?php
namespace App\Repo\Interfaces;

interface CourseInterface{

    public function getAllCourses();
    public function getCourseConfig($id);
    //getAllLangForDropdown
    public function getAllCourseForDropdown();
    public function saveCourse($request);
    public function saveCourseTranslation($request);
    public function saveCourseConfig($request);
    public function deleteCourse($id);
    public function getCourseInfoByShortName($courseShortName);



}
