<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'       => 'Intro to Programming',
                'description' => 'Learn the basics of programming logic and syntax.',
                'instructor_id' => 1,
                'category'    => 'Computer Science',
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data
        $this->db->table('courses')->insertBatch($data);
    }
}
