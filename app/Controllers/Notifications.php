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

        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'count' => 0,
                'notifications' => []
            ]);
        }

        $notifications = $notificationModel->getNotificationsForUser($userId);
        $unreadCount = $notificationModel->getUnreadCount($userId);

        return $this->response->setJSON([
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
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
