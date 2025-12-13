<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        // Load Helper JWT agar fungsi create_jwt_token() bisa dipakai
        helper(['jwt']);
    }

    // 1. Registrasi User Baru (Public)
    public function register()
    {
        $model = new UserModel();

        // Validasi input
        if (!$this->validate([
            'fullName' => 'required',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length(6)'
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        // Simpan data
        $data = [
            'fullName' => $this->request->getVar('fullName'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];

        if ($model->save($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Registrasi Berhasil. Silakan Login.']);
        }

        return $this->failServerError('Gagal menyimpan data user.');
    }

    // 2. Login User (Public -> Menghasilkan Token)
    public function login()
    {
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $pass  = $this->request->getVar('password');

        // Cari user berdasarkan email
        $user = $model->where('email', $email)->first();

        // Cek apakah user ada & password cocok
        if (!$user || !password_verify($pass, $user['password'])) {
            return $this->failUnauthorized('Email atau password salah.');
        }

        // --- GENERATE TOKEN JWT ---
        // Fungsi ini ada di app/Helpers/jwt_helper.php
        $token = create_jwt_token($user['id'], $user['email']);
        // --------------------------

        return $this->respond([
            'status' => 200,
            'message' => 'Login Berhasil',
            'token' => $token, // Token ini yang nanti dipakai user untuk request selanjutnya
            'data' => [
                'id' => $user['id'],
                'fullName' => $user['fullName'],
                'email' => $user['email']
            ]
        ]);
    }
}
