<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'message',
        'is_read'
    ];

    protected $returnType = 'array';
}