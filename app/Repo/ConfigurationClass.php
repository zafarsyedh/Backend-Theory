<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Configuration;
use App\Models\TopicArea;
use App\Traits\HandleFiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConfigurationClass implements Interfaces\ConfigurationInterface
{

    use HandleFiles;
    public function getConfigInfo()
    {

        try {
            $qry = Configuration::query();
            $qry = $qry->where('is_deleted', 0)->orderBy('id', 'DESC');
            $qry = $qry->first();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }

    public function saveConfig($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
        $logo = '';
        $res_logo = '';
        $std_bg = '';
        $path='config/';
        if ($file = $request->file('logo')) {
            $logo = $this->handleFiles($file,$path);
    }
        if ($file = $request->file('result_logo')) {
            $res_logo = $this->handleFiles($file,$path);
    }

        if(!$config=Configuration::find($request->id)){
            $config = new Configuration();
        }else{
            $config = Configuration::find($request->id);
        }

        $config->title = $request->title;
        ($logo!='')?$config->logo = $logo:'';
        ($res_logo!='')?$config->result_logo = $res_logo:'';
        $config->enable_email = $request->enable_email;
        $config->e_host = $request->e_host;
        $config->e_user_name = $request->e_user_name;
        $config->e_password = $request->e_password;
        $config->e_port = $request->e_port;
        $config->smtp_secure = $request->smtp_secure;
        $config->email_template = $request->email_template;
        $config->enable_sms = $request->enable_sms;
        $config->sms_template = $request->sms_template;
        $config->save();
            DB::commit();
            return  Helper::successWithData($config,(($id)?"Configuration Updated Successfully":"Configuration Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }
}


