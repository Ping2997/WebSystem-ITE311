<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EnrollmentModel;

class ManageStudents extends BaseController
{
    public function store()
    {
        if (!session()->get('isLoggedIn') || !in_array(session('role'), ['admin', 'teacher'], true)) {
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
            // Auto-enroll this student into all existing courses for their year level
            $studentId = (int) $userModel->getInsertID();
            $yearLevel = (string) $data['year_level'];

            if ($studentId > 0 && $yearLevel !== '') {
                $db = db_connect();
                $enrollmentModel = new EnrollmentModel();

                $courses = $db->table('courses')
                    ->select('id, capacity')
                    ->where('year_level', $yearLevel)
                    ->get()
                    ->getResultArray();

                foreach ($courses as $course) {
                    $courseId = (int) $course['id'];
                    $capacity = isset($course['capacity']) ? (int) $course['capacity'] : 0;

                    // Respect capacity if set
                    if ($capacity > 0) {
                        $currentCount = $enrollmentModel->where('course_id', $courseId)->countAllResults();
                        if ($currentCount >= $capacity) {
                            continue; // course is full
                        }
                    }

                    // Skip if already enrolled (safety)
                    if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                        continue;
                    }

                    $enrollmentModel->enrollUser([
                        'user_id'         => $studentId,
                        'course_id'       => $courseId,
                        'enrollment_date' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            session()->setFlashdata('success', 'Student created successfully and auto-enrolled to matching courses.');
        } else {
            session()->setFlashdata('error', 'Failed to create student.');
        }

        return redirect()->to(base_url('dashboard'));
    }
}
