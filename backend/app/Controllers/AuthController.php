<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    public function register()
{
    $db = \Config\Database::connect();

    $db->transStart();

    $userModel = new UserModel();
    $walletModel = new WalletModel();
    $walletTransactionModel = new WalletTransactionModel();

    $data = $this->request->getJSON(true);

    if ($userModel->where('email', $data['email'])->first()) {
        return $this->fail('Email already exists');
    }

    $referralCode = strtoupper(substr(md5(uniqid()), 0, 8));

    $userId = $userModel->insert([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'referral_code' => $referralCode,
        'referred_by' => $data['referral_code'] ?? null,
    ]);

    $balance = 0;

    if (!empty($data['referral_code'])) {

        $referrer = $userModel
            ->where('referral_code', $data['referral_code'])
            ->first();

        if ($referrer) {
            $balance = 200;
        }
    }

    $walletId = $walletModel->insert([
        'user_id' => $userId,
        'balance' => $balance,
    ]);

    if ($balance > 0) {

        $walletTransactionModel->insert([
            'wallet_id' => $walletId,
            'type' => 'credit',
            'amount' => 200,
            'description' => 'Referral bonus',
        ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
        return $this->failServerError('Registration failed');
    }

    return $this->respondCreated([
        'message' => 'Registration successful',
        'user_id' => $userId,
        'referral_code' => $referralCode,
    ]);
}

    public function login()
    {
        $userModel = new UserModel();

        helper('jwt');

        $data = $this->request->getJSON(true);

        $user = $userModel
            ->where('email', $data['email'])
            ->first();

        if (!$user) {
            return $this->failUnauthorized('Invalid credentials');
        }

        if (!password_verify($data['password'], $user['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        $token = generateJWT($user);

        return $this->respond([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]);
    }
}