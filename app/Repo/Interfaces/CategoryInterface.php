<?php
namespace App\Repo\Interfaces;

interface CategoryInterface{

    public function getAllCategory();
    public function saveCategory($request);
    public function deleteCategory($id);

    public function editCategory($id);
    public function updateCategory($request);
    public function getCourseDropdown();
    public function getSpecificCourseIdOnTheBaseOfName($courseName);
    public function saveCourseConfig($request);
    public function getCourseConfigInfo($id);
    public function getCourseConfigInfoAndQuestions();





}
