<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderQueueModel extends Model
{
    protected $table = 'order_queue';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'order_id',
        'status',
        'attempts',
        'available_at',
        'locked_by',
        'locked_at'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'array';
}