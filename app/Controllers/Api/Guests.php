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
        $userID      = $this->request->getPost('userID');
        $fullName    = $this->request->getPost('fullName');
        $email       = $this->request->getPost('email');
        $affiliation = $this->request->getPost('affiliation');
        $phone       = $this->request->getPost('phone');

        // Handle Upload Foto
        $photoUrl = '-'; // Default jika tidak ada foto
        $filePhoto = $this->request->getFile('photo');

        if ($filePhoto && $filePhoto->isValid() && ! $filePhoto->hasMoved()) {
            $newName = $filePhoto->getRandomName();
            $filePhoto->move('uploads/guests', $newName);
            $photoUrl = base_url('uploads/guests/' . $newName);
        }

        $data = [
            'userID'        => $userID,
            'fullName'      => $fullName,
            'email'         => $email,
            'affiliation'   => $affiliation,
            'phone'         => $phone,
            'photoUrl'      => $photoUrl,
            'faceEmbedding' => '',
        ];

        if ($this->model->save($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Data Tamu & Foto tersimpan', 'data' => $data]);
        }
        return $this->fail($this->model->errors());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $data ? $this->respond($data) : $this->failNotFound('Tamu tidak ditemukan');
    }

    // --- LOGIKA HAPUS DATA BESERTA FOTO ---
    public function delete($id = null)
    {
        // 1. Cari data tamu dulu
        $guest = $this->model->find($id);

        if (!$guest) {
            return $this->failNotFound('Tamu tidak ditemukan');
        }

        // 2. Hapus File Foto Fisik (Jika ada dan bukan default)
        $photoUrl = $guest['photoUrl'];
        if ($photoUrl && $photoUrl != '-' && strpos($photoUrl, 'http') !== false) {
            // Ambil nama file dari URL
            $fileName = basename($photoUrl);
            // Lokasi file di server
            $filePath = FCPATH . 'uploads/guests/' . $fileName;

            // Hapus jika file ada
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 3. Hapus data dari database
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Data Tamu dan Foto berhasil dihapus']);
        }

        return $this->failServerError('Gagal menghapus data');
    }
}
