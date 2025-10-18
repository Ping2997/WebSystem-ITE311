<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements'; // your table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'content',
        'created_at'
    ];
}
