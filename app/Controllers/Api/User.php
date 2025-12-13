<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        // Load Helper JWT untuk mengambil ID dari token
        helper(['jwt']);
    }

    // 1. Lihat Profil (Protected)
    public function profile()
    {
        // AMBIL ID DARI TOKEN (DINAMIS)
        $userID = get_current_user_id();

        if (!$userID) {
            return $this->failUnauthorized('Token invalid atau tidak ditemukan.');
        }

        $model = new UserModel();
        $user = $model->find($userID);

        if (!$user) return $this->failNotFound('User tidak ditemukan.');

        unset($user['password']); // Hapus password dari response
        return $this->respond($user);
    }

    // 2. Update Profil (Protected)
    public function updateProfile()
    {
        $userID = get_current_user_id(); // DINAMIS
        $model = new UserModel();

        $data = [
            'id' => $userID, // Pastikan yang diupdate adalah ID milik user yang login
            'fullName' => $this->request->getVar('fullName'),
            'email'    => $this->request->getVar('email')
        ];

        // Validasi simpel (opsional: tambahkan $this->validate() jika perlu)
        if ($model->save($data)) {
            return $this->respond(['status' => 200, 'message' => 'Profil berhasil diperbarui']);
        }

        return $this->fail($model->errors());
    }

    // 3. Ganti Password (Protected)
    public function changePassword()
    {
        $userID = get_current_user_id(); // DINAMIS
        $model = new UserModel();

        $oldPass = $this->request->getVar('oldPassword');
        $newPass = $this->request->getVar('newPassword');

        // Ambil data user saat ini untuk cek password lama
        $user = $model->find($userID);

        // Verifikasi password lama
        if (!password_verify($oldPass, $user['password'])) {
            return $this->fail('Password lama salah', 400);
        }

        // Simpan password baru
        $model->save([
            'id' => $userID,
            'password' => password_hash($newPass, PASSWORD_DEFAULT)
        ]);

        return $this->respond(['status' => 200, 'message' => 'Password berhasil diganti']);
    }

    // 4. Update Settings (Mockup)
    public function updateSettings()
    {
        // Contoh endpoint dummy, bisa dikembangkan simpan ke kolom 'settings' di DB
        return $this->respond(['status' => 200, 'message' => 'Pengaturan disimpan']);
    }
}
