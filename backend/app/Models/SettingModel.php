<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'processing_delay'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'array';
}