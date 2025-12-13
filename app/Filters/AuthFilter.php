<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Izinkan Preflight Request (CORS) agar tidak dicegat
        if ($request->getMethod() === 'options') {
            return;
        }

        // 2. Load Helper (Pastikan nama file jwt_helper.php)
        try {
            helper('jwt');
        } catch (\Throwable $e) {
            return Services::response()
                ->setJSON(['status' => 500, 'message' => 'Internal Error: Helper JWT tidak ditemukan.'])
                ->setStatusCode(500);
        }

        // 3. Ambil Token
        // Panggil fungsi dengan namespace global jika perlu, tapi biasanya langsung bisa
        if (!function_exists('get_token_from_header')) {
            return Services::response()
                ->setJSON(['status' => 500, 'message' => 'Internal Error: Fungsi JWT belum termuat.'])
                ->setStatusCode(500);
        }

        $token = get_token_from_header();

        if (!$token) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Akses ditolak. Token tidak ditemukan.'])
                ->setStatusCode(401);
        }

        $decoded = validate_jwt($token);
        if (!$decoded) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Token tidak valid atau expired.'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
