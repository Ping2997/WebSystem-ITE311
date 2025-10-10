<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    public function enroll()
    {
        $session = session();

        // Use unified session key set in Auth::login
        if (! $session->has('userID')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in']);
        }

        $user_id = (int) $session->get('userID');
        // Accept JSON or form-encoded
        $json = $this->request->getJSON(true);
        $course_id = (int) ($json['course_id'] ?? $this->request->getPost('course_id'));

        $enrollmentModel = new EnrollmentModel();

        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already enrolled']);
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Fetch course details for client UI update
            $course = db_connect()->table('courses')->select('id, title, description')->where('id', $course_id)->get()->getRowArray();
            return $this->response->setJSON(['success' => true, 'message' => 'Enrolled successfully', 'course' => $course]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to enroll']);
    }

    public function index()
    {
        return view('tempates/courses'); // make sure spelling matches your folder name
    }
}
