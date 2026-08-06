<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletTransactionModel extends Model
{
    protected $table = 'wallet_transactions';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'wallet_id',
        'type',
        'amount',
        'description'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'array';
}