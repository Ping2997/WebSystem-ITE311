<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'         => 1,
                'course_id'       => 1,
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'         => 2,
                'course_id'       => 1,
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data
        $this->db->table('enrollments')->insertBatch($data);
    }
}
