<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\Interest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use DateTime;

/**
 *
 */
class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return mixed|void
     */

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = $this->responseValidation($request->all(), [
                'first_name' => 'required|string|min:3|max:15|different:last_name|regex:/^[a-zA-Z]+$/',
                'last_name' => 'required|string|min:3|max:15|different:first_name|regex:/^[a-zA-Z]+$/',
                'email' => 'required|email|max:50|unique:users|regex:/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
                'dob' => 'required',
                'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[0-9])(?=.*[^\w\*])[^\s]{6,20}$/',
                'confirm_password' => 'required|min:8|same:password|regex:/^(?=.*[a-z])(?=.*[0-9])(?=.*[^\w\*])[^\s]{6,20}$/',
                'interests' => 'required',
            ],[]);

            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }

            $datetime1 = new DateTime($request->dob);
            $datetime2 = new DateTime();
            $interval = $datetime1->diff($datetime2);

            /** @var User $getUser */
            $getUser = User::where('email', $request->email)->first();
            if ($getUser && $getUser->email_verified_at != null) {
                $data['email'] = ['email already taken'];
                return $this->respondBadRequest($data);
            }
            elseif ($interval->y <= 12) {
                $data['dob'] = ['Age limit 12+'];
                return $this->respondBadRequest($data);
            }

            $array = [
                'first_name' => str_replace("-", " ", htmlspecialchars($request->input('first_name'))),
                'last_name' => str_replace("-", " ", htmlspecialchars($request->input('last_name'))),
                'email' => htmlspecialchars(strtolower($request->input('email'))),
                'password' => Hash::make($request->input('password')),
                'address' => $request->input('address'),
                'dob' => Carbon::parse($request->input('dob'))->format('Y-m-d'),
            ];
            $createdUser = User::create($array);

            if(count($request->interests) > 0){
                foreach ($request->interests as $key => $value) {
                    $int_arr = [
                        'user_id' => $createdUser->id,
                        'interest_id' => $value
                    ];
                    UserInterest::create($int_arr);
                }
            }

            if ($createdUser) {
                event(new Registered($createdUser));
                DB::commit();
                $createdUser = User::with(['interests'])->find($createdUser->id);

                $userDetail = ['user' => $createdUser];
                return $this->respond($userDetail, [], true, 'Registered Successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = $this->responseValidation($request->all(), [
                'email' => 'required|email|max:50',
                'password' => 'required|min:8'
            ],[]);

            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            $user = User::where('email', $request->input('email'))
                ->first();
            if ($user) {
                if (Hash::check($request->input('password'), $user->password)) {
                    if (!$user->hasVerifiedEmail()) {
                        return $this->respondUnauthorized([], false, 'Please Verify Email');
                    }
                    $token = $this->createUserToken($user, 'Login');
                    DB::commit();
                    $response = ['token' => $token, 'user' => $user];

                    return $this->respond($response, [], true, 'Login Successfully');
                } else {
                    return $this->respondUnauthorized([], false, 'Incorrect Email/Password');
                }
            } else {
                return $this->respondUnauthorized([], false, 'Incorrect Email/Password');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */

    /**
     * @param Request $request
     * @return mixed
     */
}
