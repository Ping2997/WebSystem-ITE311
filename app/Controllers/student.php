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

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        $userId = (int) (session('userID') ?? 0);

        $enrolledCourses = $enrollmentModel->getUserEnrollmentsDetailed($userId);

        $firstSemCourses = [];
        $secondSemCourses = [];

        foreach ($enrolledCourses as $course) {
            $sem = (string) ($course['semester'] ?? '');
            if ($sem === '2nd') {
                $secondSemCourses[] = $course;
            } else {
                // Default or "1st" go to first semester box
                $firstSemCourses[] = $course;
            }
        }

        usort($firstSemCourses, function ($a, $b) {
            return strcmp((string) ($a['start_date'] ?? ''), (string) ($b['start_date'] ?? ''));
        });

        usort($secondSemCourses, function ($a, $b) {
            return strcmp((string) ($a['start_date'] ?? ''), (string) ($b['start_date'] ?? ''));
        });

        $data = [
            'name' => session('name'),
            'email' => session('email'),
            'role' => session('role'),
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $courseModel->getAvailableCourses($userId),
            'enrolledFirstSem' => $firstSemCourses,
            'enrolledSecondSem' => $secondSemCourses,
        ];

        // Load the student dashboard view
        return view('student/dashboard', $data);
    }
}
