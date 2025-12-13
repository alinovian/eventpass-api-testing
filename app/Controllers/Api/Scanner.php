<?php

namespace App\Controllers\Api;

use App\Models\EventGuestModel;
use App\Models\AttendanceLogModel;
use App\Models\GuestModel;
use CodeIgniter\RESTful\ResourceController;

class Scanner extends ResourceController
{
    protected $format = 'json';

    // POST: Validasi QR Code
    public function validate()
    {
        $qrcode = $this->request->getVar('qrcode');
        $ticketModel = new EventGuestModel();

        $ticket = $ticketModel->getTicketDetail($qrcode);

        if (!$ticket) {
            return $this->failNotFound('QR Code Tidak Valid');
        }

        return $this->processCheckin($ticket);
    }

    // POST: Manual Check-in (Fitur Search di Mockup Scanner)
    public function manual()
    {
        $guestName = $this->request->getVar('guestName');
        $eventID   = $this->request->getVar('eventID');

        $ticketModel = new EventGuestModel();

        // Cari tiket berdasarkan Nama Tamu & Event ID
        $ticket = $ticketModel->select('eventGuests.*, guests.fullName, guestCategories.name as category')
            ->join('guests', 'guests.id = eventGuests.guestID')
            ->join('guestCategories', 'guestCategories.id = eventGuests.categoryID')
            ->where('eventGuests.eventID', $eventID)
            ->like('guests.fullName', $guestName)
            ->first();

        if (!$ticket) {
            return $this->failNotFound('Peserta tidak ditemukan di event ini');
        }

        return $this->processCheckin($ticket);
    }

    // Fungsi Private untuk memproses kehadiran (DRY Code)
    private function processCheckin($ticket)
    {
        $ticketModel = new EventGuestModel();
        $logModel    = new AttendanceLogModel();

        if ($ticket['status'] === 'hadir') {
            return $this->respond([
                'status'  => 400,
                'message' => 'Peserta SUDAH Check-in sebelumnya!',
                'data'    => $ticket
            ], 400);
        }

        // Update Status
        $ticketModel->update($ticket['id'], ['status' => 'hadir']);

        // Catat Log
        $logModel->insert([
            'eventGuestID' => $ticket['id'],
            'scannedTime'  => date('H:i:s')
        ]);

        return $this->respond([
            'status'  => 200,
            'message' => 'Check-in Berhasil',
            'data'    => $ticket
        ]);
    }
}
