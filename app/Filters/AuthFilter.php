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
        helper('jwt'); // Panggil helper yang kita buat tadi

        $token = get_token_from_header();

        // 1. Cek apakah token ada?
        if (!$token) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Akses ditolak. Token tidak ditemukan.'])
                ->setStatusCode(401);
        }

        // 2. Cek apakah token valid?
        $decoded = validate_jwt($token);
        if (!$decoded) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Token tidak valid atau sudah kadaluarsa.'])
                ->setStatusCode(401);
        }

        // Jika lolos, request diteruskan ke Controller
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah request
    }
}
