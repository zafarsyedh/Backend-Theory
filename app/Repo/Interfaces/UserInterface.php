<?php
namespace App\Repo\Interfaces;

interface UserInterface{

    public function getAllUser();

    //getInvigilator
    public function getInvigilator();
    public function createUser($request);
    public function deleteUser($id);
    public function editUser($id);
    public function updateUser($request);

}
