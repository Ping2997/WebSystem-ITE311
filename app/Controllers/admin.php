<?php

namespace App\Controllers;

class Admin extends BaseController
{ 
    public function dashboard()
    {
        // Must be logged in
        if  (!session () ->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please Login first.');
            return redirect()->to(base_url('login'));
        }

        // Must be admin
        if (session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        // Prepare courses for dashboard (moved from view)
        $courses = db_connect()->table('courses')
            ->select('id, title')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Render unified wrapper with user context and courses
        return view('Admin/admin', [
            'user' => [
                'name'  => session('name'),
                'email' => session('email'),
                'role'  => session('role'),
            ],
            'courses' => $courses,
        ]);
    }
    
    public function updateUser()
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $userId = (int) $this->request->getPost('user_id');
        if ($userId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid user ID']);
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];
        
        $yearLevel = $this->request->getPost('year_level');
        if ($yearLevel !== null && $yearLevel !== '') {
            $data['year_level'] = $yearLevel;
        } else {
            $data['year_level'] = null;
        }
        
        $userModel = new \App\Models\UserModel();
        try {
            if ($userModel->update($userId, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
            } else {
                $errors = $userModel->errors();
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user: ' . implode(', ', $errors)]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteUser($userId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $userId = (int) $userId;
        if ($userId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid user ID']);
        }
        
        $forceDelete = $this->request->getPost('force') === 'true';
        $userModel = new \App\Models\UserModel();
        
        try {
            if ($forceDelete) {
                // Permanent delete
                if ($userModel->delete($userId, true)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User permanently deleted']);
                }
            } else {
                // Soft delete (archive)
                if ($userModel->softDelete($userId)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User moved to archive. Will be permanently deleted after 30 days.']);
                }
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function restoreUser($userId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $userId = (int) $userId;
        $userModel = new \App\Models\UserModel();
        
        try {
            if ($userModel->restore($userId)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User restored successfully']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to restore user']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function updateCourse()
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $courseId = (int) $this->request->getPost('course_id');
        if ($courseId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid course ID']);
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
        ];
        
        $yearLevel = $this->request->getPost('year_level');
        if ($yearLevel !== null && $yearLevel !== '') {
            $data['year_level'] = $yearLevel;
        } else {
            $data['year_level'] = null;
        }
        
        $capacity = $this->request->getPost('capacity');
        if ($capacity !== null && $capacity !== '') {
            $data['capacity'] = (int) $capacity;
        } else {
            $data['capacity'] = null;
        }
        
        $courseModel = new \App\Models\CourseModel();
        try {
            if ($courseModel->update($courseId, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully']);
            } else {
                $errors = $courseModel->errors();
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update course: ' . implode(', ', $errors)]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteCourse($courseId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $courseId = (int) $courseId;
        if ($courseId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid course ID']);
        }
        
        $forceDelete = $this->request->getPost('force') === 'true';
        $courseModel = new \App\Models\CourseModel();
        
        try {
            if ($forceDelete) {
                // Permanent delete
                if ($courseModel->delete($courseId, true)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Course permanently deleted']);
                }
            } else {
                // Soft delete (archive)
                if ($courseModel->softDelete($courseId)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Course moved to archive. Will be permanently deleted after 30 days.']);
                }
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete course']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function restoreCourse($courseId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $courseId = (int) $courseId;
        $courseModel = new \App\Models\CourseModel();
        
        try {
            if ($courseModel->restore($courseId)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Course restored successfully']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to restore course']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteEnrollment($enrollmentId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $enrollmentId = (int) $enrollmentId;
        if ($enrollmentId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid enrollment ID']);
        }
        
        $forceDelete = $this->request->getPost('force') === 'true';
        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        try {
            if ($forceDelete) {
                // Permanent delete
                if ($enrollmentModel->delete($enrollmentId, true)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Enrollment permanently deleted']);
                }
            } else {
                // Soft delete (archive)
                if ($enrollmentModel->softDelete($enrollmentId)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Enrollment moved to archive. Will be permanently deleted after 30 days.']);
                }
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete enrollment']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function restoreEnrollment($enrollmentId)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $enrollmentId = (int) $enrollmentId;
        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        try {
            if ($enrollmentModel->restore($enrollmentId)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Enrollment restored successfully']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to restore enrollment']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function cleanupOldDeletes()
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $userModel = new \App\Models\UserModel();
        $courseModel = new \App\Models\CourseModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        try {
            $usersDeleted = $userModel->cleanupOldDeletes();
            $coursesDeleted = $courseModel->cleanupOldDeletes();
            $enrollmentsDeleted = $enrollmentModel->cleanupOldDeletes();
            
            $total = $usersDeleted + $coursesDeleted + $enrollmentsDeleted;
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Cleanup completed. Permanently deleted: $total items (Users: $usersDeleted, Courses: $coursesDeleted, Enrollments: $enrollmentsDeleted)"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}