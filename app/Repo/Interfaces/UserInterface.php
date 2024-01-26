<?php
namespace App\Repo\Interfaces;

interface UserInterface{

    public function getAllUser();
    public function createUser($request);
    public function deleteUser($id);


}
