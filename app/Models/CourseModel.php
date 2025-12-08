<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'semester', 'year_level', 'capacity', 'start_date', 'end_date', 'start_time', 'end_time', 'instructor_id', 'category', 'status'];
    protected $useTimestamps = false;

    /**
     * Get courses the user is NOT yet enrolled in. Optionally filter by search term.
     */
    public function getAvailableCourses(int $user_id, ?string $searchTerm = null): array
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

        $builder = $db->table($this->table)
            ->select('courses.id, courses.title, courses.description, courses.start_date, courses.end_date, courses.start_time, courses.end_time, courses.capacity')
            ->selectCount('enrollments.id', 'enrolled_count')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->whereNotIn('courses.id', $sub)
            ->groupBy('courses.id');

        if (!empty($yearLevel)) {
            $builder->groupStart()
                ->where('year_level', $yearLevel)
                ->orWhere('year_level', null)
            ->groupEnd();
        }

        if ($searchTerm) {
            $builder->groupStart()
                ->like('title', $searchTerm)
                ->orLike('description', $searchTerm)
            ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
