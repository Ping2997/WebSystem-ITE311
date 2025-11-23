<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description'];
    protected $useTimestamps = false;

    /**
     * Get courses the user is NOT yet enrolled in. Optionally filter by search term.
     */
    public function getAvailableCourses(int $user_id, ?string $searchTerm = null): array
    {
        $db = db_connect();
        $sub = $db->table('enrollments')->select('course_id')->where('user_id', $user_id);

        $builder = $db->table($this->table)
            ->select('id, title, description')
            ->whereNotIn('id', $sub);

        if ($searchTerm) {
            $builder->groupStart()
                ->like('title', $searchTerm)
                ->orLike('description', $searchTerm)
            ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}
