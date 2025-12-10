<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        $model = new UserModel();

        // Validasi Email Unik
        if (!$this->validate(['email' => 'required|is_unique[users.email]'])) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'fullName' => $this->request->getVar('fullName'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];

        if ($model->save($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Registrasi Berhasil']);
        }
        return $this->failServerError('Gagal menyimpan user');
    }

    public function login()
    {
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $pass  = $this->request->getVar('password');

        $user = $model->where('email', $email)->first();

        if (!$user || !password_verify($pass, $user['password'])) {
            return $this->failUnauthorized('Email atau password salah');
        }

        // Hapus password dari response agar aman
        unset($user['password']);

        return $this->respond([
            'status' => 200,
            'message' => 'Login Sukses',
            'data' => $user
        ]);
    }
}
