<?php

namespace App\Controllers\Api;

use App\Models\EventModel;
use App\Models\GuestModel;
use App\Models\EventGuestModel;
use CodeIgniter\RESTful\ResourceController;

class Events extends ResourceController
{
    protected $modelName = 'App\Models\EventModel';
    protected $format    = 'json';

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

    public function create()
    {
        // Menggunakan getVar (Bisa JSON/Form)
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

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $data['id'] = $id;

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

    // --- LOGIKA HAPUS EVENT + BERSIHKAN TAMU YATIM PIATU ---
    public function delete($id = null)
    {
        $eventGuestModel = new EventGuestModel();
        $guestModel      = new GuestModel();

        // 1. Cek apakah Event ada
        if (!$this->model->find($id)) {
            return $this->failNotFound('Event tidak ditemukan');
        }

        // 2. AMBIL DAFTAR PESERTA di event ini (Sebelum dihapus)
        // Kita butuh ID tamu mereka untuk pengecekan nanti
        $guestsInThisEvent = $eventGuestModel->where('eventID', $id)->findAll();

        // 3. Hapus Event-nya
        // (Tiket di tabel eventGuests otomatis terhapus karena settingan CASCADE di database)
        $this->model->delete($id);

        // 4. CEK & BERSIHKAN TAMU (Orphan Removal)
        $deletedGuestCount = 0;

        foreach ($guestsInThisEvent as $ticket) {
            $guestID = $ticket['guestID'];

            // Cek: Apakah Tamu ini masih punya tiket di event LAIN?
            // Kita hitung jumlah tiket dia yang tersisa di tabel eventGuests
            $sisaTiket = $eventGuestModel->where('guestID', $guestID)->countAllResults();

            if ($sisaTiket == 0) {
                // ARTINYA: Dia tidak punya event lain lagi.
                // SAATNYA HAPUS TAMU INI & FOTONYA demi kebersihan database.

                // A. Hapus Foto Fisik
                $guestData = $guestModel->find($guestID);
                if ($guestData) {
                    $photoUrl = $guestData['photoUrl'];
                    // Pastikan url valid untuk dihapus
                    if ($photoUrl && $photoUrl != '-' && strpos($photoUrl, 'http') !== false) {
                        $fileName = basename($photoUrl);
                        $filePath = FCPATH . 'uploads/guests/' . $fileName;

                        if (file_exists($filePath)) {
                            unlink($filePath); // Hapus file dari folder
                        }
                    }
                }

                // B. Hapus Data Tamu dari Tabel Guests
                $guestModel->delete($guestID);
                $deletedGuestCount++;
            }
            // Jika $sisaTiket > 0, biarkan saja (karena dia masih aktif di event lain)
        }

        return $this->respondDeleted([
            'status' => 200,
            'message' => "Event dihapus. $deletedGuestCount tamu yang tidak terdaftar di event lain juga telah dihapus bersih."
        ]);
    }
}
