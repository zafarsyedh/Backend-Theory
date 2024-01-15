<?php
namespace App\Repo\Interfaces;

interface TopicAreaInterface{

    public function getAllTopics();
    public function createTopics($request);
    public function saveTopicTranslation($request);
    public function deleteTopics($id);
    public function editTopics($id);
    public function updateTopics($request);
    public function getAllTopicAreaForDropdown();
    public function getTopicMaxId();

}
