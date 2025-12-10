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
        $allEnrollments = $enrollModel->getCourseEnrollmentsWithUsers($courseId);
        $pendingEnrollments = $enrollModel->getPendingEnrollments($courseId);
        $approvedEnrollments = $enrollModel->getApprovedEnrollments($courseId);
        $enrolledCount = count($approvedEnrollments);

        return view('courses/detail', [
            'course'             => $course,
            'enrolledStudents'   => $allEnrollments,
            'pendingEnrollments' => $pendingEnrollments,
            'approvedEnrollments' => $approvedEnrollments,
            'enrolledCount'      => $enrolledCount,
        ]);
    }

    public function approveEnrollment($enrollment_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('role');
        if ($role !== 'admin' && $role !== 'teacher') {
            return redirect()->to(base_url('dashboard'));
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify teacher owns the course
        $courseId = (int) $enrollment['course_id'];
        $course = db_connect()->table('courses')
            ->where('id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        $userId = (int) ($session->get('userID') ?? 0);
        if ($role === 'teacher' && (int) ($course['instructor_id'] ?? 0) !== $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // Check capacity before approving
        $capacity = isset($course['capacity']) ? (int) $course['capacity'] : 0;
        if ($capacity > 0) {
            $approvedCount = $enrollmentModel->where('course_id', $courseId)
                ->where('approval_status', 'approved')
                ->countAllResults();
            if ($approvedCount >= $capacity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course is already at full capacity.',
                ]);
            }
        }

        if ($enrollmentModel->updateApprovalStatus($enrollment_id, 'approved')) {
            // Send notifications to all relevant roles
            try {
                $notifModel = new NotificationModel();
                $db = db_connect();
                $courseTitle = $course['title'] ?? 'the course';
                $studentId = (int) $enrollment['user_id'];
                
                // Get student info
                $studentRow = $db->table('users')
                    ->select('username')
                    ->where('id', $studentId)
                    ->get()
                    ->getRowArray();
                $studentName = $studentRow['username'] ?? 'A student';
                
                // Notify student
                $notifModel->sendNotification($studentId, 'Your enrollment request for ' . $courseTitle . ' has been approved!');
                
                // Notify all admins
                $notifModel->sendNotificationToRole('admin', $studentName . '\'s enrollment request for ' . $courseTitle . ' has been approved.');
            } catch (\Throwable $e) {
                log_message('error', 'Approval notification failed: ' . $e->getMessage());
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment approved']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to approve enrollment']);
    }

    public function rejectEnrollment($enrollment_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('role');
        if ($role !== 'admin' && $role !== 'teacher') {
            return redirect()->to(base_url('dashboard'));
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify teacher owns the course
        $courseId = (int) $enrollment['course_id'];
        $course = db_connect()->table('courses')
            ->where('id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        $userId = (int) ($session->get('userID') ?? 0);
        if ($role === 'teacher' && (int) ($course['instructor_id'] ?? 0) !== $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($enrollmentModel->updateApprovalStatus($enrollment_id, 'rejected')) {
            // Send notifications to all relevant roles
            try {
                $notifModel = new NotificationModel();
                $db = db_connect();
                $courseTitle = $course['title'] ?? 'the course';
                $studentId = (int) $enrollment['user_id'];
                
                // Get student info
                $studentRow = $db->table('users')
                    ->select('username')
                    ->where('id', $studentId)
                    ->get()
                    ->getRowArray();
                $studentName = $studentRow['username'] ?? 'A student';
                
                // Notify student
                $notifModel->sendNotification($studentId, 'Your enrollment request for ' . $courseTitle . ' has been rejected.');
                
                // Notify all admins
                $notifModel->sendNotificationToRole('admin', $studentName . '\'s enrollment request for ' . $courseTitle . ' has been rejected.');
            } catch (\Throwable $e) {
                log_message('error', 'Rejection notification failed: ' . $e->getMessage());
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment rejected']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to reject enrollment']);
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

        // Validate course_id
        if ($course_id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid course selected.']);
        }

        $enrollmentModel = new EnrollmentModel();

        // Check if course exists and is active
        $db = db_connect();
        $courseRow = $db->table('courses')
            ->select('id, capacity, status')
            ->where('id', $course_id)
            ->get()
            ->getRowArray();

        if (!$courseRow) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found.']);
        }

        if ($courseRow['status'] !== 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'This course is not available for enrollment.']);
        }

        // Check if user has any enrollment (pending, approved, or rejected)
        if ($enrollmentModel->hasEnrollment($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'You have already submitted an enrollment request for this course.']);
        }

        // Enforce course capacity if set (only count approved enrollments)

        $capacity = isset($courseRow['capacity']) ? (int) $courseRow['capacity'] : 0;
        if ($capacity > 0) {
            $approvedCount = $enrollmentModel->where('course_id', $course_id)
                ->where('approval_status', 'approved')
                ->countAllResults();
            if ($approvedCount >= $capacity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This course is already full.',
                ]);
            }
        }

        // Verify user is a student (only students can enroll)
        $userRow = $db->table('users')
            ->select('role')
            ->where('id', $user_id)
            ->get()
            ->getRowArray();
        
        if (!$userRow || $userRow['role'] !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only students can enroll in courses.']);
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'approval_status' => 'pending'
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Create notifications for all roles
            try {
                $notifModel = new NotificationModel();
                
                // Get course and instructor info
                $courseRow = $db->table('courses')
                    ->select('courses.title, courses.instructor_id, users.id as instructor_user_id')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->where('courses.id', $course_id)
                    ->get()
                    ->getRowArray();
                
                $courseTitle = $courseRow['title'] ?? 'the course';
                $instructorId = $courseRow['instructor_user_id'] ?? null;
                
                // Get student info
                $studentRow = $db->table('users')
                    ->select('username')
                    ->where('id', $user_id)
                    ->get()
                    ->getRowArray();
                $studentName = $studentRow['username'] ?? 'A student';
                
                // Notify student that enrollment request is pending
                $notifModel->sendNotification($user_id, 'Your enrollment request for ' . $courseTitle . ' is pending teacher approval.');
                
                // Notify teacher about new enrollment request
                if ($instructorId) {
                    $notifModel->sendNotification($instructorId, $studentName . ' has requested to enroll in ' . $courseTitle);
                }
                
                // Notify all admins about new enrollment request
                $notifModel->sendNotificationToRole('admin', $studentName . ' has requested to enroll in ' . $courseTitle);
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

        // Always return JSON (search is handled via AJAX)
        return $this->response->setJSON(['courses' => $courses, 'search_term' => (string) $searchTerm]);
    }

    public function store()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('role');
        if ($role !== 'admin') {
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

        // If admin, instructor_id is required and must be a valid teacher
        if ($role === 'admin') {
            $rules['instructor_id'] = 'required|integer';
        }

        if (! $validation->setRules($rules)->withRequest($this->request)->run()) {
            $session->setFlashdata('error', 'Please fix the errors in the form.');
            return redirect()->back()->withInput();
        }

        // Admin can assign any teacher
        $instructorId = (int) ($this->request->getPost('instructor_id') ?? 0);
        if ($instructorId <= 0) {
            $session->setFlashdata('error', 'Please select a teacher for this course.');
            return redirect()->back()->withInput();
        }
        // Verify the selected user is actually a teacher
        $db = db_connect();
        $teacherCheck = $db->table('users')
            ->where('id', $instructorId)
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->countAllResults();
        if ($teacherCheck === 0) {
            $session->setFlashdata('error', 'Invalid teacher selected.');
            return redirect()->back()->withInput();
        }

        // Validate date range if both dates are provided
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        if (!empty($startDate) && !empty($endDate)) {
            $startTimestamp = strtotime($startDate);
            $endTimestamp = strtotime($endDate);
            if ($endTimestamp < $startTimestamp) {
                $session->setFlashdata('error', 'End date must be after start date.');
                return redirect()->back()->withInput();
            }
        }

        // Validate time range if both times are provided
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        if (!empty($startTime) && !empty($endTime) && !empty($startDate) && !empty($endDate)) {
            // If same day, check time order
            if ($startDate === $endDate) {
                $startDateTime = strtotime($startDate . ' ' . $startTime);
                $endDateTime = strtotime($endDate . ' ' . $endTime);
                if ($endDateTime <= $startDateTime) {
                    $session->setFlashdata('error', 'End time must be after start time when dates are the same.');
                    return redirect()->back()->withInput();
                }
            }
        }

        $data = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'semester'     => $this->request->getPost('semester'),
            'year_level'   => $this->request->getPost('year_level'),
            'capacity'     => $this->request->getPost('capacity') ?: null,
            'start_date'   => $startDate ?: null,
            'end_date'     => $endDate ?: null,
            'start_time'   => $startTime ?: null,
            'end_time'     => $endTime ?: null,
            'instructor_id'=> $instructorId,
            'category'     => 'General',
            'status'       => 'active',
        ];

        if ($courseModel->insert($data)) {
            $courseId = $courseModel->getInsertID();
            $courseTitle = $data['title'] ?? 'New course';
            
            // Send notifications to all relevant roles
            try {
                $notifModel = new NotificationModel();
                $db = db_connect();
                
                // Get assigned teacher info
                $teacherRow = $db->table('users')
                    ->select('username, first_name, last_name')
                    ->where('id', $instructorId)
                    ->get()
                    ->getRowArray();
                $teacherName = !empty($teacherRow['first_name']) || !empty($teacherRow['last_name'])
                    ? trim(($teacherRow['first_name'] ?? '') . ' ' . ($teacherRow['last_name'] ?? ''))
                    : ($teacherRow['username'] ?? 'A teacher');
                
                // Admin created the course - notify the assigned teacher, students, and admin
                // Notify the assigned teacher
                $notifModel->sendNotification($instructorId, 'You have been assigned to teach the course: ' . $courseTitle);
                
                // Notify all students about new course availability
                $notifModel->sendNotificationToRole('student', 'A new course "' . $courseTitle . '" taught by ' . $teacherName . ' is now available for enrollment.');
                
                // Notify admin about the course creation
                $adminId = (int) (session('userID') ?? 0);
                if ($adminId > 0) {
                    $notifModel->sendNotification($adminId, 'Course "' . $courseTitle . '" has been created and assigned to ' . $teacherName . '.');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Course creation notification failed: ' . $e->getMessage());
            }
        }

        $session->setFlashdata('success', 'Course created successfully.');
        return redirect()->to(base_url('dashboard'));
    }
}
