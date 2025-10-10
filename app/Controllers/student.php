<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Student extends BaseController
{
    public function dashboard()
    {
        // Must be logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        // Must be Student
        if (session('role') !== 'student') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        // Load models
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        $userId = session('id');

        // Get user data
        $data = [
            'name' => session('name'),
            'email' => session('email'),
            'role' => session('role'),
            'enrolledCourses' => $enrollmentModel->getUserEnrollments($userId),
            'availableCourses' => $courseModel->getAvailableCourses($userId)
        ];

        // Load the student dashboard view
        return view('student/dashboard', $data);
    }
}
