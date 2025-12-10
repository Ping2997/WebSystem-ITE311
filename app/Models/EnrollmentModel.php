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
     * Get students enrolled in a specific course with basic user info.
     */
    public function getCourseEnrollmentsWithUsers(int $course_id): array
    {
        return $this->select('users.id, users.username, users.email, users.year_level')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $course_id)
            ->orderBy('users.username', 'ASC')
            ->findAll();
    }

    /**
     * Get user's enrolled courses with course title/description.
     */
    public function getUserEnrollmentsDetailed(int $user_id): array
    {
        return $this->select('courses.id, courses.title, courses.description, courses.semester, courses.start_date, courses.end_date, courses.start_time, courses.end_time, users.username AS instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = courses.instructor_id', 'left')
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

        // Get student's year level
        $userRow = $db->table('users')
            ->select('year_level')
            ->where('id', $user_id)
            ->get()
            ->getRowArray();

        $yearLevel = $userRow['year_level'] ?? null;

        $builder = $db->table('courses')
            ->select('courses.id, courses.title, courses.description, courses.start_date, courses.end_date, courses.start_time, courses.end_time, courses.capacity, users.username AS instructor_name')
            ->selectCount('enrollments.id', 'enrolled_count')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->whereNotIn('courses.id', $sub)
            ->groupBy('courses.id');

        // If student has a year level, only show matching courses (or those with no year set)
        if (!empty($yearLevel)) {
            $builder->groupStart()
                ->where('courses.year_level', $yearLevel)
                ->orWhere('courses.year_level', null)
            ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
