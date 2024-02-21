<?php
namespace App\Repo\Interfaces;

interface SystemInterface{

    public function getAllSystems();
    public function createSystem($request);
    public function deleteSystem($id);
    public function getAllRoomForDropdown();

    public function getRoomWiseSystems($roomId);
    public function updateSystemStatus($systemId,$status);


}
