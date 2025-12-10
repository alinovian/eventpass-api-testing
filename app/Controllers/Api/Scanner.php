<?php

namespace App\Controllers\Api;

use App\Models\EventGuestModel;
use App\Models\AttendanceLogModel;
use CodeIgniter\RESTful\ResourceController;

class Scanner extends ResourceController
{
    protected $format = 'json';

    // POST: Validasi QR Code
    public function validate()
    {
        $qrcode = $this->request->getVar('qrcode');

        $ticketModel = new EventGuestModel();
        $logModel    = new AttendanceLogModel();

        // 1. Cari Data Tiket Full
        $ticket = $ticketModel->getTicketDetail($qrcode);

        if (!$ticket) {
            return $this->failNotFound('QR Code Tidak Valid / Tidak Ditemukan');
        }

        // 2. Cek apakah sudah check-in?
        if ($ticket['status'] === 'hadir') {
            return $this->respond([
                'status'  => 400,
                'message' => 'PERINGATAN: Peserta ini SUDAH Check-in sebelumnya!',
                'data'    => $ticket
            ], 400);
        }

        // 3. Update Status jadi 'hadir'
        $ticketModel->update($ticket['id'], ['status' => 'hadir']);

        // 4. Catat Log Waktu
        $logModel->insert([
            'eventGuestID' => $ticket['id'],
            'scannedTime'  => date('H:i:s') // Format Time MySQL
        ]);

        return $this->respond([
            'status'  => 200,
            'message' => 'Check-in BERHASIL! Selamat Datang.',
            'data'    => $ticket
        ]);
    }
}
