<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => $now,
            ]);


            if($user->save()){
                return response()->json([
                    'success' => true,
                    'message' => '['.$request->username.'] 사용자 생성 완료!'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => '사용자 생성 실패!'
                ]);
            }



        }catch(\Exception $e) {

                return response()->json([
                    'error' => $e->getMessage()
                ], 401);
        }
    }

    /**
     * Login user and create token
     *
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|min:12|max:20',
            'password' => 'required|string',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        ;
        //$user = Auth::user();

        if (Auth::attempt(['user_id' => $request->user_id, 'password' =>$request->password])) {
            $token = auth()->user()->createToken('appToken')->accessToken;
            Auth::login(Auth::user());
            return response()->json([
                'success' => true,
                'token' => $token,
            ]);
        } else {
            $response = ["message" =>'사용자가 존재하지 않습니다.'];
            return response()->json([
                'success' => false,
                $response
            ], 422);
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
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
        /*if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => '성공적으로 로그아웃되었습니다.',
            ], 200);
        }
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json([
                'success' => true,
                'message' => '성공적으로 로그아웃되었습니다.'
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => '인증되지 않았습니다.'
            ],401);
        }*/

        /*$result = $request->user()->token()->revoke();
        if($result){
            $response = response()->json(['error'=>false,'message'=>'User logout successfully.'],200);
        }else{
            $response = response()->json(['error'=>true,'message'=>'Something is wrong.'],401);
        }
        return $response;*/

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
