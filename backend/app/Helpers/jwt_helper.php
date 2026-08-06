<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($user)
{
    $key = env('JWT_SECRET', 'my_super_secret_key');

    $payload = [
        'iss' => 'bao-ecommerce',
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24),
        'data' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function validateJWT($token)
{
    $key = env('JWT_SECRET', 'my_super_secret_key');

    return JWT::decode($token, new Key($key, 'HS256'));
}