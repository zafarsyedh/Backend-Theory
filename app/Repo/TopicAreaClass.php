<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TopicAreaClass implements Interfaces\TopicAreaInterface
{

    public function getAllTopicAreaForDropdown()
    {
        try {
            $qry=TopicArea::with('topicAreaTranslation');
            $qry=$qry->where('status',1);
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }
    public function getAllTopics()
    {
        try {
            $qry=TopicArea::with('topicAreaTranslation');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }


    public function createTopics($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'full_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('topic_area_translations')->whereNull('deleted_at') . $id,
                ],
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = TopicArea::updateOrCreate(
                [
                    'id' => $request->id,
                ],

                [
                    'status' =>$request->status,

                ]
            );
            if($role)
            {
                $trans = TopicAreaTranslation::updateOrCreate(
                    [
                        'topic_area_id' => $role->id,
                    ],
                    [
                        'topic_area_id' => $role->id,
                        'full_name' =>$request->full_name,
                        'lang' =>'en',
                    ]
                );

            }

            DB::commit();
            $data = TopicArea::with("topicAreaTranslation")->find($role->id);
            return  Helper::successWithData($data,(($id)?"Topic Area Updated Successfully":"Topic Area Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }

    public function saveTopicTranslation($request)
    {

        try {
            $id=$request->topic_area_id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'topic_area_id' => 'required',
                'lang' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());


            for ($c = 0; $c < count($request['full_name']); $c++) {

                $role = TopicAreaTranslation::updateOrCreate(
                    [
                        'topic_area_id' => $request->topic_area_id,
                        'lang' => $request['lang'][$c],
                    ],

                    [
                        'full_name' =>$request['full_name'][$c],
                        'topic_area_id' => $request->topic_area_id,
                        'lang' => $request['lang'][$c],
                    ]
                );

            }
            DB::commit();
            $data = TopicArea::with("topicAreaTranslation")->find($id);
            return  Helper::successWithData($data,(($id)?"Topic Area Translation Updated Successfully":"Topic Area Translation Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }


    public function deleteTopics($id)
    {


        try {
            DB::beginTransaction();
            $role = TopicArea::find($id);
            $role->delete();
            $qtr=TopicAreaTranslation::where('topic_area_id',$id)->delete();
            DB::commit();
            return Helper::successWithData($role, $message="Topic Area Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }


}
