<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Guests extends ResourceController
{
    protected $modelName = 'App\Models\GuestModel';
    protected $format    = 'json';

    public function index()
    {
        $userID = $this->request->getVar('userID');
        $data = $userID ? $this->model->where('userID', $userID)->findAll() : $this->model->findAll();
        return $this->respond($data);
    }

    public function create()
    {
        // 1. Ambil Data Teks (Menggunakan getPost karena Form Data)
        $userID      = $this->request->getPost('userID');
        $fullName    = $this->request->getPost('fullName');
        $email       = $this->request->getPost('email');
        $affiliation = $this->request->getPost('affiliation');
        $phone       = $this->request->getPost('phone'); // Field Baru

        // 2. Handle Upload Foto
        $photoUrl = null;
        $filePhoto = $this->request->getFile('photo'); // Nama field di form: 'photo'

        if ($filePhoto && $filePhoto->isValid() && ! $filePhoto->hasMoved()) {
            // Generate nama file acak
            $newName = $filePhoto->getRandomName();
            // Pindahkan ke folder public/uploads/guests
            $filePhoto->move('uploads/guests', $newName);
            // Simpan URL lengkapnya
            $photoUrl = base_url('uploads/guests/' . $newName);
        } else {
            // Jika tidak upload, pakai gambar default
            $photoUrl = '-';
        }

        // 3. Susun Data
        $data = [
            'userID'        => $userID,
            'fullName'      => $fullName,
            'email'         => $email,
            'affiliation'   => $affiliation,
            'phone'         => $phone,     // Masuk ke database
            'photoUrl'      => $photoUrl,  // Masuk ke database
            'faceEmbedding' => '',         // Kosongkan dulu
        ];

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => 201,
                'message' => 'Data Tamu & Foto tersimpan',
                'data' => $data
            ]);
        }
        return $this->fail($this->model->errors());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $data ? $this->respond($data) : $this->failNotFound('Tamu tidak ditemukan');
    }
}
