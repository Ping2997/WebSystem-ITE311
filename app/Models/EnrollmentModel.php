<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date', 'approval_status', 'deleted_at'];
    protected $useTimestamps = false;

    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    public function getUserEnrollments($user_id)
    {
        // Kept method name for backward compatibility but uses user_id column
        return $this->where('user_id', $user_id)->findAll();
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->first() ? true : false;
    }

    /**
     * Get students enrolled in a specific course with basic user info.
     */
    public function getCourseEnrollmentsWithUsers(int $course_id): array
    {
        return $this->select('enrollments.id, enrollments.approval_status, users.id as user_id, users.username, users.email, users.year_level')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $course_id)
            ->orderBy('enrollments.approval_status', 'ASC')
            ->orderBy('users.username', 'ASC')
            ->findAll();
    }

    /**
     * Get pending enrollments for a course
     */
    public function getPendingEnrollments(int $course_id): array
    {
        return $this->select('enrollments.id, enrollments.approval_status, users.id as user_id, users.username, users.email, users.year_level')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $course_id)
            ->where('enrollments.approval_status', 'pending')
            ->orderBy('enrollments.enrollment_date', 'ASC')
            ->findAll();
    }

    /**
     * Get approved enrollments for a course
     */
    public function getApprovedEnrollments(int $course_id): array
    {
        return $this->select('enrollments.id, enrollments.approval_status, users.id as user_id, users.username, users.email, users.year_level')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $course_id)
            ->where('enrollments.approval_status', 'approved')
            ->orderBy('users.username', 'ASC')
            ->findAll();
    }

    /**
     * Check if user is approved for a course
     */
    public function isApproved(int $user_id, int $course_id): bool
    {
        $enrollment = $this->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->where('approval_status', 'approved')
            ->first();
        return $enrollment ? true : false;
    }

    /**
     * Update enrollment approval status
     */
    public function updateApprovalStatus(int $enrollment_id, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }
        return $this->update($enrollment_id, ['approval_status' => $status]);
    }

    /**
     * Get user's enrolled courses with course title/description.
     * Only returns approved enrollments - pending/rejected enrollments are not shown.
     */
    public function getUserEnrollmentsDetailed(int $user_id): array
    {
        return $this->select('courses.id, courses.title, courses.description, courses.semester, courses.start_date, courses.end_date, courses.start_time, courses.end_time, enrollments.approval_status, users.username AS instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.approval_status', 'approved')
            ->findAll();
    }

    /**
     * Check if enrollment exists (regardless of approval status)
     */
    public function hasEnrollment(int $user_id, int $course_id): bool
    {
        return $this->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->first() ? true : false;
    }

    /**
     * Get courses the user is NOT yet enrolled in (no enrollment record at all).
     */
    public function getAvailableCourses(int $user_id): array
    {
        $db = db_connect();
        // Exclude courses where user has any enrollment status
        $sub = $db->table('enrollments')->select('course_id')->where('user_id', $user_id);

        // Get student's year level
        $userRow = $db->table('users')
            ->select('year_level')
            ->where('id', $user_id)
            ->get()
            ->getRowArray();

        $yearLevel = $userRow['year_level'] ?? null;

        $builder = $db->table('courses')
            ->select('courses.id, courses.title, courses.description, courses.start_date, courses.end_date, courses.start_time, courses.end_time, courses.capacity')
            ->selectCount('enrollments.id', 'enrolled_count')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', $sub)
            ->groupBy('courses.id');

        // If student has a year level, only show matching courses (or those with no year set)
        if (!empty($yearLevel)) {
            $builder->groupStart()
                ->where('year_level', $yearLevel)
                ->orWhere('year_level', null)
            ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get all pending enrollments for a teacher's courses
     */
    public function getTeacherPendingEnrollments(int $teacher_id): array
    {
        $db = db_connect();
        
        return $db->table('enrollments')
            ->select('enrollments.id, enrollments.enrollment_date, enrollments.approval_status, 
                     users.id as user_id, users.username, users.email, users.year_level,
                     courses.id as course_id, courses.title as course_title')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('courses.instructor_id', $teacher_id)
            ->where('courses.status', 'active')
            ->where('enrollments.approval_status', 'pending')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get only deleted enrollments
     */
    public function getDeletedEnrollments(): array
    {
        $db = db_connect();
        return $db->table('enrollments')
            ->select('id, user_id, course_id, enrollment_date, approval_status, deleted_at')
            ->where('deleted_at IS NOT NULL', null, false)
            ->orderBy('deleted_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Soft delete enrollment
     */
    public function softDelete($id): bool
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Restore deleted enrollment
     */
    public function restore($id): bool
    {
        return $this->update($id, ['deleted_at' => null]);
    }
    
    /**
     * Permanently delete enrollments deleted more than 30 days ago
     */
    public function cleanupOldDeletes(): int
    {
        $db = db_connect();
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $deleted = $db->table('enrollments')
            ->where('deleted_at IS NOT NULL', null, false)
            ->where('deleted_at <', $thirtyDaysAgo)
            ->get()
            ->getResultArray();
        
        $count = 0;
        foreach ($deleted as $enrollment) {
            if ($db->table('enrollments')->delete(['id' => $enrollment['id']])) {
                $count++;
            }
        }
        return $count;
    }
}
