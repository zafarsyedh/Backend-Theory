<?php
namespace App\Repo\Interfaces;

interface RoomInterface{

    public function getAllRooms();
    public function createRoom($request);
    public function deleteRoom($id);
    public function getAllRoomForDropdown();


}
