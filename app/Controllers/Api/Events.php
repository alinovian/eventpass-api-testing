<?php

namespace App\Controllers\Api;

use App\Models\EventModel;
use CodeIgniter\RESTful\ResourceController;

class Events extends ResourceController
{
    protected $modelName = 'App\Models\EventModel';
    protected $format    = 'json';

    // GET: Ambil semua event
    public function index()
    {
        $userID = $this->request->getVar('userID');
        if ($userID) {
            $data = $this->model->where('userID', $userID)->findAll();
        } else {
            $data = $this->model->findAll();
        }
        return $this->respond($data);
    }

    // POST: Tambah Event
    public function create()
    {
        // getVar otomatis baca JSON atau Form Data
        $data = [
            'userID'      => $this->request->getVar('userID'),
            'name'        => $this->request->getVar('name'),
            'date'        => $this->request->getVar('date'),
            'time'        => $this->request->getVar('time'),
            'location'    => $this->request->getVar('location'),
            'description' => $this->request->getVar('description'),
        ];

        if ($this->model->save($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Event berhasil dibuat']);
        }
        return $this->fail($this->model->errors());
    }

    // PUT: Update Event
    public function update($id = null)
    {
        // Ambil data (support JSON untuk PUT)
        $data = [
            'id'          => $id,
            'userID'      => $this->request->getVar('userID'),
            'name'        => $this->request->getVar('name'),
            'date'        => $this->request->getVar('date'),
            'time'        => $this->request->getVar('time'),
            'location'    => $this->request->getVar('location'),
            'description' => $this->request->getVar('description'),
        ];

        // Hapus field yang kosong (agar tidak menimpa data lama dengan null)
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        if ($this->model->save($data)) {
            return $this->respond(['status' => 200, 'message' => 'Event berhasil diupdate']);
        }
        return $this->fail($this->model->errors());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $data ? $this->respond($data) : $this->failNotFound('Event tidak ditemukan');
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Event berhasil dihapus']);
        }
        return $this->failNotFound('Event tidak ditemukan');
    }
}
