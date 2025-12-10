<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    public function get()
    {
        $notificationModel = new NotificationModel();
        $userId = (int) (session()->get('userID') ?? 0);
        $role = session()->get('role') ?? 'unknown';

        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'count' => 0,
                'notifications' => [],
                'debug' => 'No userID in session'
            ]);
        }

        try {
            $notifications = $notificationModel->getNotificationsForUser($userId);
            $unreadCount = $notificationModel->getUnreadCount($userId);

            // Ensure proper JSON response with correct headers
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'count' => $unreadCount,
                    'notifications' => $notifications,
                    'debug' => [
                        'user_id' => $userId,
                        'role' => $role,
                        'notifications_found' => count($notifications)
                    ]
                ]);
        } catch (\Throwable $e) {
            log_message('error', 'Notification fetch error: ' . $e->getMessage());
            return $this->response
                ->setContentType('application/json')
                ->setStatusCode(500)
                ->setJSON([
                    'count' => 0,
                    'notifications' => [],
                    'error' => 'Failed to fetch notifications',
                    'debug' => [
                        'user_id' => $userId,
                        'role' => $role,
                        'message' => $e->getMessage()
                    ]
                ]);
        }
    }

    public function mark_as_read($id)
    {
        $notificationModel = new NotificationModel();

        if ($notificationModel->markAsRead($id)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }
}
