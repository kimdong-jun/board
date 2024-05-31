<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|string|min:12|max:20|unique:users|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*?_])/',
            'user_name' => 'required|string',
            'user_email' => 'required|email:rfc,dns',
            'user_pw' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute은(는) 필수 입력 항목입니다.',
            'min' => ':attribute은(는) 최소 :min 글자 이상이 필요합니다.',
            'max' => ':attribute은(는) 최소 :max 글자 이하로 필요합니다.',
            'email' => ':attribute은(는) 이메일 형식으로 작성해주세요.',
            'unique' => ':attribute은(는) 이미 사용중입니다.',
            'regex' => ':attribute은(는) 영어 대문자, 소문자, 특수문자를 포함하여야 합니다.'
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => '아이디',
            'user_name' => '이름',
            'user_email' => '이메일',
            'user_pw' => '비밀번호'
        ];
    }


}
