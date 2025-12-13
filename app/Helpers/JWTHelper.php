<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\IncomingRequest;

/**
 * 1. Membuat Token (Cetak Kartu)
 * Dipanggil saat Login berhasil
 */
function create_jwt_token($userId, $email)
{
    $key = getenv('JWT_SECRET');
    $time = time();
    $ttl = getenv('JWT_TTL');

    $payload = [
        'iat' => $time,          // Waktu dibuat (Issued At)
        'exp' => $time + $ttl,   // Waktu kadaluarsa (Expiration Time)
        'uid' => $userId,        // Data User ID disimpan di sini
        'email' => $email        // Data Email disimpan di sini
    ];

    return JWT::encode($payload, $key, 'HS256');
}

/**
 * 2. Mengambil Token dari Header
 * Mengambil string token dari "Authorization: Bearer <token>"
 */
function get_token_from_header()
{
    $request = service('request');
    $header = $request->getHeaderLine('Authorization');

    if (!$header) return null;

    // Ambil teks setelah kata "Bearer "
    if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        return $matches[1];
    }

    return null;
}

/**
 * 3. Validasi Token (Cek Keaslian)
 * Dipanggil oleh Filter (Satpam)
 */
function validate_jwt($token)
{
    try {
        $key = getenv('JWT_SECRET');
        // Decode token. Jika palsu atau expired, akan error (masuk catch)
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 4. Ambil User ID Pengguna Saat Ini
 * Dipanggil di Controller untuk tahu "Siapa yang sedang akses?"
 */
function get_current_user_id()
{
    $token = get_token_from_header();
    if (!$token) return null;

    $decoded = validate_jwt($token);
    return $decoded ? $decoded->uid : null;
}
