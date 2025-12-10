<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;

class ManageTeachers extends BaseController
{
    public function store()
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username'   => 'required|min_length[2]|max_length[100]|is_unique[users.username]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'first_name' => 'permit_empty|max_length[100]',
            'last_name'  => 'permit_empty|max_length[100]',
            'department' => 'required|max_length[100]',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('teacher_validation', $validation);
        }

        $userModel = new UserModel();
        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'department' => $this->request->getPost('department'),
            'role'       => 'teacher',
            'status'     => 'active',
        ];

        if ($userModel->insert($data)) {
            $teacherId = $userModel->getInsertID();
            $teacherUsername = $data['username'] ?? 'New teacher';
            $teacherName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            if (empty($teacherName)) {
                $teacherName = $teacherUsername;
            }
            
            // Send notifications
            try {
                $notifModel = new NotificationModel();
                $currentUserId = (int) (session('userID') ?? 0);
                
                // Notify the newly created teacher
                $notifModel->sendNotification($teacherId, 'Welcome! Your teacher account has been created. You can now log in and manage courses.');
                
                // Notify admin about the teacher creation
                if ($currentUserId > 0) {
                    $notifModel->sendNotification($currentUserId, 'Teacher "' . $teacherName . '" (' . $teacherUsername . ') has been successfully created.');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Teacher creation notification failed: ' . $e->getMessage());
            }
            
            session()->setFlashdata('success', 'Teacher created successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to create teacher.');
        }

        return redirect()->to(base_url('dashboard'));
    }
}
