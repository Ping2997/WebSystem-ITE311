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

    /**
     * Send notification to a single user
     */
    public function sendNotification(int $user_id, string $message): bool
    {
        try {
            return $this->insert([
                'user_id'    => $user_id,
                'message'    => $message,
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Notification send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all users with a specific role
     */
    public function sendNotificationToRole(string $role, string $message): int
    {
        $db = db_connect();
        $users = $db->table('users')
            ->select('id, username')
            ->where('role', $role)
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        $count = 0;
        foreach ($users as $user) {
            if ($this->sendNotification((int) $user['id'], $message)) {
                $count++;
            } else {
                // Log if notification failed for a specific user
                log_message('debug', 'Failed to send notification to ' . $role . ' user ID: ' . $user['id'] . ' (' . ($user['username'] ?? 'unknown') . ')');
            }
        }
        
        // Log summary
        if ($count === 0 && !empty($users)) {
            log_message('warning', 'No notifications sent to ' . $role . ' role. Found ' . count($users) . ' users but all sends failed.');
        } elseif (empty($users)) {
            log_message('info', 'No active ' . $role . ' users found to send notifications to.');
        }
        
        return $count;
    }

    /**
     * Send notification to multiple specific users
     */
    public function sendNotificationToUsers(array $user_ids, string $message): int
    {
        $count = 0;
        foreach ($user_ids as $user_id) {
            if ($this->sendNotification((int) $user_id, $message)) {
                $count++;
            }
        }
        return $count;
    }
}
