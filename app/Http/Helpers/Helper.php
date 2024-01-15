<?php
namespace App\Http\Helpers;
use Illuminate\Http\Exceptions\HttpResponseException;

class Helper{
    public static function sendError($message, $errors= [], $code = 400)
    {
        $response= ['success'=>false, 'status'=>400, 'code'=>$code, 'message' => $message];

        if(!empty($errors)){
            $response['data'] = $errors;
        }
        throw new HttpResponseException(response()->json($response,$code));
    }


    public static function vaidationError($status,$errors= [], $message =[])
    {
        $response= [ 'status'=>$status, 'messege' => $message];

        if(!empty($errors)){
            $response['data'] = $errors;
        }
        return $response;
    }


    public static function createAPIResponce($is_error, $code, $message, $content)
    {
        $result = [];
        if ($is_error) {
            $result['success'] = false;
            $result['code'] = $code;
            $result['message'] = $message;
            $result['data'] = $content;
        } else {
            $result['success'] = true;
            $result['code'] = $code;
            $result['message'] = $message;
            if ($content == null) {
                $result['message'] = $message;
            } else {
                $result['data'] = $content;
            }
        }
        return $result;
    }

    public static function ajaxError($message = 'success')
    {
        try {
            return response()->json(collect(['status' => false, 'message' => $message]), 400);
        } catch (\Exception $exception) {
            return response()->json(collect(['status' => false, 'message' => $exception->getMessage()]), 400);
        }
    }




//    public static function sendError($message, $errors = [], $code = 401)
//    {
//        $response = ['success' => false, 'message' => $message];
//        if (!empty($errors)) {
//            $response['data'] = $errors;
//        }
//        throw new HttpResponseException(response()->json($response, $code));
//    }

//    public static function createAPIResponce($is_error, $code, $message, $content)
//    {
//        $result = [];
//        if ($is_error) {
//            $result['success'] = false;
//            $result['code'] = $code;
//            $result['message'] = $message;
//        } else {
//            $result['success'] = true;
//            $result['code'] = $code;
//            if ($content == null) {
//                $result['message'] = $message;
//            } else {
//                $result['data'] = $content;
//            }
//        }
//        return $result;
//    }

    // below functions created by Ahsan
    public static function success($data, $message = 'success')
    {

        try {
            return collect(['status' => true, 'data'=> $data, 'message' => $message]);
        } catch (\Exception $exception) {
            return collect(['status' => false, 'data' => null, 'message' => $exception->getMessage()]);
        }
    }

    public static function error($message = 'success')
    {
        try {
            return collect(['status' => false, 'message' => $message]);
        } catch (\Exception $exception) {
            return collect(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    public static function errorWithData($message = 'error', $data)
    {
        try {
            return collect(['status' => false, 'data' => $data, 'message' => $message]);
        } catch (\Exception $exception) {
            return collect(['status' => false, 'data' => null, 'message' => $exception->getMessage()]);
        }
    }

    public static function ajaxSuccess($data, $message = 'success')
    {
        try {
            return response()->json(collect(['status' => true, 'data'=> $data, 'message' => $message]), 200);
        } catch (\Exception $exception) {
            return response()->json(collect(['status' => false, 'data' => null, 'message' => $exception->getMessage()]), 400);
        }
    }

//    public static function ajaxError($message = 'success')
//    {
//        try {
//            return response()->json(collect(['status' => false, 'message' => $message]), 400);
//        } catch (\Exception $exception) {
//            return response()->json(collect(['status' => false, 'message' => $exception->getMessage()]), 400);
//        }
//    }

    public static function ajaxErrorWithData($message = 'error', $data)
    {
        try {
            return response()->json(collect(['status' => false, 'data' => $data, 'message' => $message]));
        } catch (\Exception $exception) {
            return response()->json(collect(['status' => false, 'data' => null, 'message' => $exception->getMessage()]));
        }
    }

    public static function ajaxDatatable($data, $totalRecords, $request){

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $data->count(),
            "recordsFiltered" =>$totalRecords,
            "data" => $data,

        ]);


    }


    public static function fetchOnlyData($data){
        try {
            return $data->get('data');
        } catch (\Exception $exception) {
            return response()->json(collect(['status' => false, 'message' => $exception->getMessage()]), 400);
        }
    }






}
