<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\EnrollmentModel;

class Teacher extends BaseController
{
    public function approveStudent($student_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $teacherId = (int) ($session->get('userID') ?? 0);
        $studentId = (int) $student_id;
        
        $db = db_connect();
        
        // Get student info
        $student = $db->table('users')
            ->where('id', $studentId)
            ->where('role', 'student')
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        
        if (!$student) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student not found']);
        }
        
        $studentYearLevel = $student['year_level'] ?? null;
        
        // Get teacher's courses matching student's year level
        $matchingCourses = $db->table('courses')
            ->select('id, title')
            ->where('instructor_id', $teacherId)
            ->where('year_level', $studentYearLevel)
            ->where('status', 'active')
            ->get()
            ->getResultArray();
        
        if (empty($matchingCourses)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'You do not have any courses matching this student\'s year level.'
            ]);
        }
        
        // Automatically enroll student in all matching courses with approved status
        $enrollmentModel = new EnrollmentModel();
        $enrolledCount = 0;
        $courseTitles = [];
        
        foreach ($matchingCourses as $course) {
            $courseId = (int) $course['id'];
            
            // Check if already enrolled
            if (!$enrollmentModel->hasEnrollment($studentId, $courseId)) {
                // Check course capacity before enrolling
                $courseRow = $db->table('courses')
                    ->select('capacity')
                    ->where('id', $courseId)
                    ->get()
                    ->getRowArray();
                
                $capacity = isset($courseRow['capacity']) ? (int) $courseRow['capacity'] : 0;
                if ($capacity > 0) {
                    $approvedCount = $enrollmentModel->where('course_id', $courseId)
                        ->where('approval_status', 'approved')
                        ->countAllResults();
                    if ($approvedCount >= $capacity) {
                        continue; // Skip this course, it's full
                    }
                }
                
                // Create enrollment with approved status
                $enrollmentData = [
                    'user_id' => $studentId,
                    'course_id' => $courseId,
                    'enrollment_date' => date('Y-m-d H:i:s'),
                    'approval_status' => 'approved'
                ];
                
                if ($enrollmentModel->enrollUser($enrollmentData)) {
                    $enrolledCount++;
                    $courseTitles[] = $course['title'];
                } else {
                    // Log enrollment failure
                    $errors = $enrollmentModel->errors();
                    log_message('error', 'Failed to enroll student ' . $studentId . ' in course ' . $courseId . ': ' . json_encode($errors));
                }
            }
        }
        
        // Send notification to student
        try {
            $notifModel = new NotificationModel();
            $studentName = $student['username'] ?? 'Student';
            
            if ($enrolledCount > 0) {
                $coursesList = implode(', ', $courseTitles);
                $notifModel->sendNotification(
                    $studentId,
                    'You have been approved and automatically enrolled in ' . $enrolledCount . ' course(s): ' . $coursesList
                );
            } else {
                $notifModel->sendNotification(
                    $studentId,
                    'You have been approved by a teacher.'
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'Student approval notification failed: ' . $e->getMessage());
        }
        
        if ($enrolledCount > 0) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Student "' . ($student['username'] ?? 'Student') . '" has been approved and enrolled in ' . $enrolledCount . ' course(s).'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Student "' . ($student['username'] ?? 'Student') . '" has been approved. (All matching courses are full or student already enrolled)'
            ]);
        }
    }
}