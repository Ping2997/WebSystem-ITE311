<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function getNotificationsForUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0) 
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
    }

    public function markAsRead($id)
    {
        return $this->update($id, ['is_read' => 1]);
    }
}
