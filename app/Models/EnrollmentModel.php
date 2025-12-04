<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
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
     * Get user's enrolled courses with course title/description.
     */
    public function getUserEnrollmentsDetailed(int $user_id): array
    {
        return $this->select('courses.id, courses.title, courses.description, courses.semester, courses.start_date, courses.end_date, courses.start_time, courses.end_time')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->findAll();
    }

    /**
     * Get courses the user is NOT yet enrolled in.
     */
    public function getAvailableCourses(int $user_id): array
    {
        $db = db_connect();
        $sub = $db->table('enrollments')->select('course_id')->where('user_id', $user_id);

        return $db->table('courses')
            ->select('id, title, description, start_date, end_date, start_time, end_time')
            ->whereNotIn('id', $sub)
            ->get()
            ->getResultArray();
    }
}
