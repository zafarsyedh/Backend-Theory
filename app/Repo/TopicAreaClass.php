<?php

namespace App\Repo;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TopicAreaClass implements Interfaces\TopicAreaInterface
{

    public function getAllTopicAreaForDropdown()
    {
        $qry=TopicArea::with('topicAreaTranslation');
        $qry=$qry->where('status',1);
        $qry=$qry->get();
        return $qry;

    }
    public function getAllTopics()
    {
        $qry=TopicArea::with('topicAreaTranslation');
        $qry=$qry->get();
        return $qry;
    }


    public function createTopics($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];

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
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => (($id)?"Topic Area Updated Successfully":"Topic Area Added Successfully")
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $validationException->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $e->getMessage()
            ];
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
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];


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
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => "Topic Area Translation Added Successfully"
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $validationException->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $e->getMessage()
            ];
        }
    }






    public function deleteTopics($id)
    {

        $res=DB::transaction(function() use ($id) {
                $addon =TopicArea::find($id);
                $addon->delete();
                });

                 return 1;

    }

    public function editTopics($id)
    {
        // TODO: Implement editAddon() method.
        return $category = User::find($id);
    }

    public function updateTopics($request)
    {


        // TODO: Implement updateAddon() method.
        $category=User::find($request->id);
        $category->lang_id=$request->lang_id;
        $category->cat_title=$request->cat_title;
        $category->status=$request->status;
        $category->save();
        return 1;
    }
    public  function  getTopicMaxId()
    {
        // TODO: Implement getTopicMaxId() method.

        return $maxId=TopicArea::max('id');
    }
}
