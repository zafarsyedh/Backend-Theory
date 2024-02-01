<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LanguageClass implements Interfaces\LanguageInterface
{

    public function getAllLanguages()
    {
        try {
            $qry=Language::Query();
            $qry=$qry->orderBy('is_default','DESC');;
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getAllLangForDropdown()
    {
        try {
            $qry=Language::Query();
            $qry=$qry->where('status',1)->orderBy('is_default','DESC');;
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }

    public function saveLanguage($request)
    {

        try {

            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'lang' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('languages')->whereNull('deleted_at') . $id,
                ],
                'lang_short' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('languages')->whereNull('deleted_at') . $id,
                ],
                'direction' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = Language::updateOrCreate(
                [
                    'id' => $request->id,
                ],
                [
                    'lang' => $request->lang,
                    'lang_short' =>$request->lang_short,
                    'is_default' =>($request->lang_short == "en") ? 1 : 0,
                    'direction' =>$request->direction,
                    'status' =>$request->status,
                ]
            );
            DB::commit();
            return  Helper::successWithData($role,(($id)?"Language Updated Successfully":"Language Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }

    }

    public function deleteLanguage($id)
    {
        try {
            $role = Language::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="Language Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }

}
