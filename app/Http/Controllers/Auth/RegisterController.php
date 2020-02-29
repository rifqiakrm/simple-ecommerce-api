<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
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

    public function register()
    {
        $rules = [
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'role' => 'required|in:user,merchant',
        ];

        $messages = [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak sesuai.',
            'password.required' => 'Password tidak boleh kosong.',
            'role.required' => 'Role tidak boleh kosong.',
            'role.in' => 'Role tidak ditemukan.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        $check = $this->userExist($this->request->email);

        if (!$check) {
            $user = User::create([
                'name' => $this->request->name,
                'email' => $this->request->email,
                'password' => Hash::make($this->request->password),
                'balance' => 0,
                'points' => 0,
            ]);

            Role::create([
                'user_id' => $user->id,
                'role' => strtolower($this->request->role),
            ]);

            return $this->successResponse(201, "Registrasi berhasil", null);
        } else {
            return $this->errorResponse(400, "Email telah digunakan");
        }
    }
}
