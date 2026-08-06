<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'referral_code',
        'referred_by'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'array';
}