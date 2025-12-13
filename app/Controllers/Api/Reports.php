<?php

namespace App\Controllers\Api;

use App\Models\EventModel;
use App\Models\EventGuestModel;
use App\Models\AttendanceLogModel;
use CodeIgniter\RESTful\ResourceController;

class Reports extends ResourceController
{
    protected $format = 'json';

    // Mockup: Halaman List Laporan (Ringkasan Persentase)
    public function index()
    {
        $eventModel = new EventModel();
        $db = \Config\Database::connect();

        // Query manual untuk hitung persentase kehadiran per event
        $sql = "SELECT e.id, e.name, e.date, e.time,
                (SELECT COUNT(*) FROM eventGuests WHERE eventID = e.id) as total_guests,
                (SELECT COUNT(*) FROM eventGuests WHERE eventID = e.id AND status = 'hadir') as total_present
                FROM events e";

        $query = $db->query($sql);
        $results = $query->getResultArray();

        // Hitung persentase
        foreach ($results as &$row) {
            $row['percentage'] = $row['total_guests'] > 0
                ? round(($row['total_present'] / $row['total_guests']) * 100)
                : 0;
        }

        return $this->respond($results);
    }

    // Mockup: Halaman Detail Laporan (Statistik Card)
    public function show($id = null)
    {
        $eventModel = new EventModel();
        $ticketModel = new EventGuestModel();

        $event = $eventModel->find($id);
        if (!$event) return $this->failNotFound('Event tidak ditemukan');

        $totalGuests = $ticketModel->where('eventID', $id)->countAllResults();
        $totalPresent = $ticketModel->where('eventID', $id)->where('status', 'hadir')->countAllResults();
        $totalAbsent = $totalGuests - $totalPresent;

        $response = [
            'event' => $event,
            'statistics' => [
                'total_guests' => $totalGuests,
                'present' => $totalPresent,
                'absent' => $totalAbsent,
                'percentage' => $totalGuests > 0 ? round(($totalPresent / $totalGuests) * 100) : 0
            ]
        ];

        return $this->respond($response);
    }

    // Mockup: Tabel Log Kehadiran di bawah Detail Laporan
    public function logs($eventID = null)
    {
        $logModel = new AttendanceLogModel();

        // Join dari Logs -> EventGuest -> Guest -> Event
        $data = $logModel->select('attendancelogs.scannedTime, guests.fullName, guestcategories.name as category')
            ->join('eventguests', 'eventguests.id = attendancelogs.eventGuestID')
            ->join('guests', 'guests.id = eventguests.guestID')
            ->join('guestcategories', 'guestcategories.id = eventguests.categoryID')
            ->where('eventguests.eventID', $eventID)
            ->orderBy('attendancelogs.scannedTime', 'DESC')
            ->findAll();

        return $this->respond($data);
    }

    // Data Realtime untuk Dashboard
    public function realtime($eventID = null)
    {
        return $this->show($eventID); // Gunakan logika yang sama dengan detail report
    }
}
