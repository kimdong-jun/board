<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        /**
         *
         * success : 성공시 200 반환
         * token : 성공시 토큰 값 반환
         * error : 실패시 에러
         * message : 에러 메시지 반환
         */
    }

    /**
     * Create user
     *
     * @param  [string] user_id
     * @param  [string] user_name
     * @param  [string] user_email
     * @param  [string] user_pw
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(UsersRequest $request)
    {
        try {
            $now = date('Y-m-d H:i:s');

            $user = new Users([
                'user_id' => $request->user_id,
                'user_name' => $request->user_name,
                'user_email' => $request->user_email,
                'user_pw' => bcrypt($request->user_pw),
                'user_regDt' => $now,
            ]);
            $user->save();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => '['.$request->user_name.'] 사용자 생성 완료!'
            ], 200);

        }catch(\Exception $e) {

                return response()->json([
                    'error' => $e->getMessage()
                ], 401);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|min:12|max:20',
            'user_pw' => 'required|string',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = Users::where('user_id', $request->user_id)->first();

        if ($user) {
            if (Hash::check($request->user_pw, $user->user_pw)) {
                $success['token'] = $user->createToken('appToken')->accessToken;
                return response($success, 200, (array)"Content-Type:application/json");

            } else {
                $response = ["message" => "패스워드가 일치하지 않습니다.."];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'사용자가 존재하지 않습니다.'];
            return response($response, 422);
        }

    }

    public function createToken ($userName, $password) {
        try {


            $data = [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'km4BTZNIWfenpwBdEz9wO8pNZQTZbZJo1B4qfxb7',
                'username' => $userName,
                'password' => $password,
                'scope' => '*',
            ];

            $request = Request::create('/oauth/token', 'POST', $data);
            var_dump($request);
            $response = app()->handle($request);

            return $response;
        } catch(\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 401);
        }

    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {

        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ],200);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
