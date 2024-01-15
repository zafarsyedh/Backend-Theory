<?php

namespace App\Repo;
use App\Models\Language;
class LanguageClass implements Interfaces\LanguageInterface
{

    public function getAllLanguages()
    {
        $qry=Language::Query();
        $qry=$qry->where('is_deleted',0)
            ->orderBy('is_default','DESC');
//        $qry=$qry->paginate(5);
        $qry=$qry->get();
        return $qry;

    }
    public function getAllLangForDropdown()
    {
        $qry=Language::Query();
        $qry=$qry->where('is_deleted',0);
        $qry=$qry->where('status',1)
            ->orderBy('is_default','DESC');
        $qry=$qry->get();
        return $qry;

    }

    public function saveLanguage($request)
    {
        // TODO: Implement saveAddon() method.
        if(Language::where('lang',$request->lang)->where('is_deleted',0)->first()){
            return $response=[
                "status"=>"false",
                "messege"=>"This record already exist"
            ];
        }
        $language=new Language();
        $language->lang=$request->lang;
        $language->lang_short=$request->short_code;
        $language->direction=$request->direction;
        $language->status=$request->status;
        if($language->save()){
            return $response=([
                "status"=>"success",
                "data"=>$language,
                "messege"=>"Languages Added Successfully"
            ]);
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }
    }

    public function deleteLanguage($id)
    {
        // TODO: Implement deleteAddon() method.
        $addon =Language::find($id);
        $addon->is_deleted=1;
        $addon->save();
        return 1;
    }

    public function editLanguage($id)
    {
        // TODO: Implement editAddon() method.
        return $addon = Language::find($id);
    }

    public function updateLanguage($request)
    {
        // TODO: Implement updateAddon() method.
        $language=Language::find($request->id);
        $language->lang=$request->lang;
        $language->direction=($request->direction);
        $language->status=$request->status;
        if($language->save()){
            return $response=([
                "status"=>"success",
                "data"=>$language,
                "messege"=>"Languages Updated Successfully"
            ]);
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }
    }
}
