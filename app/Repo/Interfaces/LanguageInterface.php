<?php
namespace App\Repo\Interfaces;

interface LanguageInterface{

    public function getAllLanguages();
    //getAllLangForDropdown
    public function getAllLangForDropdown();
    public function saveLanguage($request);
    public function deleteLanguage($id);


}
