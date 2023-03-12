<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    public function getInfo(Request $request){
        try{
            $getRecord = User::with(['interests:name'])->find($request->user()->id);
            // $data['full_name'] = $getRecord['full_name'];
            // $data['dob'] = $getRecord['date_of_birth'];
            // $data['interests'] = $getRecord['interests'];
            return $this->respond($getRecord,[],true,'Data Retrieved Successfully');
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
}
