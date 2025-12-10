<?php

namespace App\Controllers\Api;

use App\Models\EventGuestModel;
use CodeIgniter\RESTful\ResourceController;

class Ticket extends ResourceController
{
    protected $modelName = 'App\Models\EventGuestModel';
    protected $format    = 'json';

    // POST: Daftarkan Tamu ke Event (Buat Tiket)
    public function create()
    {
        $eventID = $this->request->getVar('eventID');
        $guestID = $this->request->getVar('guestID');

        // Generate QR Unik: EV{id}-GST{id}-RANDOM
        $qrString = 'EV' . $eventID . '-GST' . $guestID . '-' . substr(md5(time()), 0, 5);

        $data = [
            'eventID'    => $eventID,
            'guestID'    => $guestID,
            'categoryID' => $this->request->getVar('categoryID'),
            'qrcode'     => $qrString,
            'status'     => 'belum_hadir'
        ];

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Tiket Berhasil Dibuat',
                'qrcode'  => $qrString
            ]);
        }
        return $this->fail($this->model->errors());
    }

    // GET: Lihat Peserta di Event tertentu
    public function listByEvent($eventID = null)
    {
        $data = $this->model->select('eventGuests.*, guests.fullName, guestCategories.name as category')
            ->join('guests', 'guests.id = eventGuests.guestID')
            ->join('guestCategories', 'guestCategories.id = eventGuests.categoryID')
            ->where('eventID', $eventID)
            ->findAll();

        return $this->respond($data);
    }
}
