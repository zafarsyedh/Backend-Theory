<?php
namespace App\Repo\Interfaces;

interface ConfigurationInterface{

    public function getConfigInfo();
    public function saveConfig($request);


}
