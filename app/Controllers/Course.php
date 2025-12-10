<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    public function detail($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('role');
        if ($role !== 'admin' && $role !== 'teacher') {
            return redirect()->to(base_url('dashboard'));
        }

        $courseId = (int) $id;
        $db = db_connect();

        $course = $db->table('courses')
            ->select('courses.*, users.username AS instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->to(base_url('dashboard'));
        }

        // Teachers may only view their own courses
        $userId = (int) ($session->get('userID') ?? 0);
        if ($role === 'teacher' && $userId > 0 && (int) ($course['instructor_id'] ?? 0) !== $userId) {
            return redirect()->to(base_url('dashboard'));
        }

        $enrollModel = new EnrollmentModel();
        $enrolledStudents = $enrollModel->getCourseEnrollmentsWithUsers($courseId);
        $enrolledCount = count($enrolledStudents);

        return view('courses/detail', [
            'course'           => $course,
            'enrolledStudents' => $enrolledStudents,
            'enrolledCount'    => $enrolledCount,
        ]);
    }

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

        // Enforce course capacity if set
        $courseRow = db_connect()->table('courses')
            ->select('capacity')
            ->where('id', $course_id)
            ->get()
            ->getRowArray();

        $capacity = isset($courseRow['capacity']) ? (int) $courseRow['capacity'] : 0;
        if ($capacity > 0) {
            $currentCount = $enrollmentModel->where('course_id', $course_id)->countAllResults();
            if ($currentCount >= $capacity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This course is already full.',
                ]);
            }
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Create a notification for the student
            try {
                $notifModel = new NotificationModel();
                // Get course title for a friendly message
                $courseRow = db_connect()->table('courses')->select('title')->where('id', $course_id)->get()->getRowArray();
                $courseTitle = $courseRow['title'] ?? 'the course';
                $notifModel->insert([
                    'user_id'    => $user_id,
                    'message'    => 'You have been enrolled in ' . $courseTitle,
                    'is_read'    => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // Do not block enrollment on notification failure; optionally log
                log_message('error', 'Enroll notification failed: ' . $e->getMessage());
            }

            // Fetch course details for client UI update
            $course = db_connect()->table('courses')
                ->select('id, title, description, semester, start_date, end_date, start_time, end_time')
                ->where('id', $course_id)
                ->get()
                ->getRowArray();
            return $this->response->setJSON(['success' => true, 'message' => 'Enrolled successfully', 'course' => $course]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to enroll']);
    }

    public function index()
    {
        return view('tempates/courses'); // make sure spelling matches your folder name
    }

    public function search()
    {
        $session = session();
        $userId = (int) ($session->get('userID') ?? 0);

        $searchTerm = $this->request->getGet('search_term');
        if ($searchTerm === null) {
            $searchTerm = $this->request->getPost('search_term');
        }

        $courseModel = new CourseModel();

        // If user is logged in, show only available (not yet enrolled) courses; otherwise search all
        if ($userId > 0) {
            $courses = $courseModel->getAvailableCourses($userId, $searchTerm);
        } else {
            $builder = $courseModel->select('id, title, description');
            if (!empty($searchTerm)) {
                $builder->groupStart()
                    ->like('title', $searchTerm)
                    ->orLike('description', $searchTerm)
                ->groupEnd();
            }
            $courses = $builder->findAll();
        }

        if ($this->request->isAJAX() || ($this->request->getHeaderLine('Accept') && strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false)) {
            return $this->response->setJSON(['courses' => $courses, 'search_term' => (string) $searchTerm]);
        }

        return view('courses/search_results', ['courses' => $courses, 'searchTerm' => $searchTerm]);
    }

    public function store()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('role');
        if ($role !== 'admin' && $role !== 'teacher') {
            return redirect()->to(base_url('dashboard'));
        }

        $courseModel = new CourseModel();

        $validation = \Config\Services::validation();
        $rules = [
            'title' => 'required|min_length[3]|max_length[200]',
            'semester' => 'required|in_list[1st,2nd]',
            'year_level' => 'required',
            'capacity' => 'permit_empty|integer|greater_than[0]',
            'start_date' => 'permit_empty|valid_date',
            'end_date' => 'permit_empty|valid_date',
            // Time fields: allow empty or raw time strings; CI4 has no built-in valid_time rule
            'start_time' => 'permit_empty',
            'end_time' => 'permit_empty',
        ];

        // Admin must choose which teacher will handle the course
        if ($role === 'admin') {
            $rules['instructor_id'] = 'required|integer';
        }

        if (! $validation->setRules($rules)->withRequest($this->request)->run()) {
            $session->setFlashdata('error', 'Please fix the errors in the form.');
            return redirect()->back()->withInput();
        }

        // Instructor ID: for teachers use themselves, for admin use the selected teacher
        $instructorId = (int) ($session->get('userID') ?? 0);
        if ($role === 'admin') {
            $instructorId = (int) $this->request->getPost('instructor_id');
        }

        $data = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'semester'     => $this->request->getPost('semester'),
            'year_level'   => $this->request->getPost('year_level'),
            'capacity'     => $this->request->getPost('capacity') ?: null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'start_time'   => $this->request->getPost('start_time') ?: null,
            'end_time'     => $this->request->getPost('end_time') ?: null,
            'instructor_id'=> $instructorId,
            'category'     => 'General',
            'status'       => 'active',
        ];

        // Insert course
        $courseModel->insert($data);
        $courseId = (int) $courseModel->getInsertID();

        // Auto-enroll all students for this year level (if any)
        if ($courseId > 0) {
            $db = db_connect();
            $enrollmentModel = new EnrollmentModel();

            // Determine course capacity (0 or null means unlimited)
            $capacity = 0;
            if (!empty($data['capacity'])) {
                $capacity = (int) $data['capacity'];
            }

            // Fetch all students with matching year_level
            $builder = $db->table('users')
                ->select('id')
                ->where('role', 'student')
                ->where('status', 'active')
                ->where('year_level', $data['year_level']);

            $students = $builder->get()->getResultArray();

            foreach ($students as $stu) {
                $userId = (int) $stu['id'];

                // Respect capacity if set
                if ($capacity > 0) {
                    $currentCount = $enrollmentModel->where('course_id', $courseId)->countAllResults();
                    if ($currentCount >= $capacity) {
                        break; // course is full
                    }
                }

                // Skip if already enrolled (safety for re-runs)
                if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                    continue;
                }

                $enrollmentModel->enrollUser([
                    'user_id'         => $userId,
                    'course_id'       => $courseId,
                    'enrollment_date' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $session->setFlashdata('success', 'Course created successfully and students for that year have been auto-enrolled.');
        return redirect()->to(base_url('dashboard'));
    }
}
