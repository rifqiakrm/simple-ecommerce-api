<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request User Request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->posted = $request->except('_token', '_method');
    }

    public function login()
    {
        $rules = [
            'email' => 'email|required',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak sesuai.',
            'password.required' => 'Password tidak boleh kosong.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        if (Auth::attempt(['email' => $this->request->email, 'password' => $this->request->password])) {
            $user = User::where('email', $this->request->email)->first();
            if ($user) {
                $token = $user->createToken('Login');

                return $this->successResponse(200, 'success', [
                    "token_type" => "Bearer",
                    "token" => $token->accessToken,
                ]);
            } else {
                return $this->errorResponse(404, "User tidak ditemukan");
            }
        } else {
            return $this->errorResponse(400, "Email atau password salah");
        }
    }
}
