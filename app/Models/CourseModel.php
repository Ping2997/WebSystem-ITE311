<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'semester', 'year_level', 'capacity', 'start_date', 'end_date', 'start_time', 'end_time', 'instructor_id', 'category', 'status', 'deleted_at'];
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
            ->select('courses.id, courses.title, courses.description, courses.start_date, courses.end_date, courses.start_time, courses.end_time, courses.capacity, users.username AS instructor_name')
            ->selectCount('enrollments.id', 'enrolled_count')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', $sub)
            ->groupBy('courses.id');

        if (!empty($yearLevel)) {
            $builder->groupStart()
                ->where('courses.year_level', $yearLevel)
                ->orWhere('courses.year_level', null)
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
    
    /**
     * Get only deleted courses
     */
    public function getDeletedCourses(): array
    {
        $db = db_connect();
        return $db->table('courses')
            ->select('id, title, description, status, year_level, capacity, deleted_at')
            ->where('deleted_at IS NOT NULL', null, false)
            ->orderBy('deleted_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Soft delete course
     */
    public function softDelete($id): bool
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Restore deleted course
     */
    public function restore($id): bool
    {
        return $this->update($id, ['deleted_at' => null]);
    }
    
    /**
     * Permanently delete courses deleted more than 30 days ago
     */
    public function cleanupOldDeletes(): int
    {
        $db = db_connect();
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $deleted = $db->table('courses')
            ->where('deleted_at IS NOT NULL', null, false)
            ->where('deleted_at <', $thirtyDaysAgo)
            ->get()
            ->getResultArray();
        
        $count = 0;
        foreach ($deleted as $course) {
            if ($db->table('courses')->delete(['id' => $course['id']])) {
                $count++;
            }
        }
        return $count;
    }
}
