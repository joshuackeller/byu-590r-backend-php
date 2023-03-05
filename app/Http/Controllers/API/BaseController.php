<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Storage;


class BaseController extends Controller
{
   /**
    * success response method.
    *
    * @return \Illuminate\Http\Response
    */
   public function sendResponse($result, $message)
   {
       $response = [
           'success' => true,
           'data'    => $result,
           'message' => $message,
       ];


       return response()->json($response, 200);
   }


   /**
    * return error response.
    *
    * @return \Illuminate\Http\Response
    */
   public function sendError($error, $errorMessages = [], $code = 404)
   {
       $response = [
           'success' => false,
           'message' => $error,
       ];


       if(!empty($errorMessages)){
           $response['data'] = $errorMessages;
       }


       return response()->json($response, $code);
   }

   public function getS3Url($path,$minutes=10)
   {
       if(!$path) {
           return null;
       }
       $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
       return $url;
   }
}
