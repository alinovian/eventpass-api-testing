<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function create_jwt_token($userId, $email)
{
    $key = getenv('JWT_SECRET');
    $time = time();
    $ttl = getenv('JWT_TTL') ?: 3600;

    $payload = [
        'iat' => $time,
        'exp' => $time + $ttl,
        'uid' => $userId,
        'email' => $email
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function get_token_from_header()
{
    $request = service('request');
    // Ambil header Authorization
    $header = $request->header('Authorization'); // Menggunakan method header() yang lebih aman

    if (!$header) return null;

    // Ambil value dari object Header
    $tokenString = $header->getValue();

    if (preg_match('/Bearer\s(\S+)/', $tokenString, $matches)) {
        return $matches[1];
    }

    return null;
}

function validate_jwt($token)
{
    try {
        $key = getenv('JWT_SECRET');
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        return false;
    }
}

function get_current_user_id()
{
    $token = get_token_from_header();
    if (!$token) return null;

    $decoded = validate_jwt($token);
    return $decoded ? $decoded->uid : null;
}
