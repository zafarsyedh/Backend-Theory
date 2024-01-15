<?php

namespace App\Repo;
use App\Models\Configuration;
use App\Traits\HandleFiles;

class ConfigurationClass implements Interfaces\ConfigurationInterface
{

    use HandleFiles;
    public function getConfigInfo()
    {
        $qry = Configuration::query();
        $qry = $qry->where('is_deleted', 0)->orderBy('id', 'DESC');
        $qry = $qry->first();
        return $qry;

    }

    public function saveConfig($request)
    {

        $logo = '';
        $admin_bg = '';
        $std_bg = '';
        $path='config/';
        if ($file = $request->file('logo')) {
            $logo = $this->handleFiles($file,$path);
    }


        if(!$config=Configuration::find($request->id)){
            $config = new Configuration();
        }else{
            $config = Configuration::find($request->id);
        }

        $config->title = $request->title;
        ($logo!='')?$config->logo = $logo:'';
        $config->enable_email = $request->enable_email;
        $config->e_host = $request->e_host;
        $config->e_user_name = $request->e_user_name;
        $config->e_password = $request->e_password;
        $config->e_port = $request->e_port;
        $config->smtp_secure = $request->smtp_secure;
        $config->email_template = $request->email_template;
        $config->enable_sms = $request->enable_sms;
        $config->sms_template = $request->sms_template;

        if($config->save()){
            return $response=([
                "status"=>"success",
                "data"=>$config,
                "messege"=>"Configuration Added Successfully"
            ]);
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }
    }
}


