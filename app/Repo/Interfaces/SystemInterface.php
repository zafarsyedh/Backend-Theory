<?php
namespace App\Repo\Interfaces;

interface SystemInterface{

    public function getAllSystems();
    public function createSystem($request);
    public function deleteRoom($id);
    public function getAllRoomForDropdown();


}
