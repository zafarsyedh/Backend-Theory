<?php
namespace App\Repo\Interfaces;

interface StudentInterface{

    public function getAllStudent();
    public function saveStudent($request);
    public function deleteStudent($id);

    public function editStudent($id);
    public function updateStudent($request);
    public function getAllStdDropdown();
    public function getStdWithTrafficId($trafficId);
    public function studentLogedHistory($stdId,$staffId);


    public function chekStdExamSchudle($stdId);


}
