<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;

class ManageStudents extends BaseController
{
    public function store()
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[2]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'year_level' => 'required',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            // Redirect back to dashboard so the Add Student modal can show errors
            return redirect()->back()->withInput()->with('student_validation', $validation);
        }

        $userModel = new UserModel();
        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role'       => 'student',
            'status'     => 'active',
            'year_level' => $this->request->getPost('year_level'),
        ];

        if ($userModel->insert($data)) {
            $studentId = $userModel->getInsertID();
            $studentUsername = $data['username'] ?? 'New student';
            
            // Send notifications when admin/teacher adds a student
            try {
                $notifModel = new NotificationModel();
                $db = db_connect();
                $currentUserId = (int) (session('userID') ?? 0);
                
                // Notify the newly created student
                $notifModel->sendNotification($studentId, 'Welcome! Your account has been created. You can now log in and enroll in courses.');
                
                // Notify teachers who have courses matching the student's year level
                $studentYearLevel = $data['year_level'] ?? null;
                if ($studentYearLevel) {
                    $teachers = $db->table('courses')
                        ->select('courses.instructor_id, users.username as teacher_name')
                        ->join('users', 'users.id = courses.instructor_id', 'inner')
                        ->where('courses.year_level', $studentYearLevel)
                        ->where('courses.status', 'active')
                        ->where('users.role', 'teacher')
                        ->where('users.status', 'active')
                        ->groupBy('courses.instructor_id')
                        ->get()
                        ->getResultArray();
                    
                    foreach ($teachers as $teacher) {
                        $notifModel->sendNotification(
                            (int) $teacher['instructor_id'],
                            'A new ' . $studentYearLevel . ' student (' . $studentUsername . ') has been added. You can review and approve them for your courses.'
                        );
                    }
                }
                
                // Notify admin about the student creation
                if ($currentUserId > 0) {
                    $notifModel->sendNotification($currentUserId, 'Student "' . $studentUsername . '" (' . ($studentYearLevel ?? 'N/A') . ') has been successfully created.');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Student creation notification failed: ' . $e->getMessage());
            }
            
            session()->setFlashdata('success', 'Student created successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to create student.');
        }

        return redirect()->to(base_url('dashboard'));
    }
}
