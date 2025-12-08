<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AllSeeder extends Seeder
{
    public function run()
    {
        // Order matters if there are foreign keys:
        // 1. Users (teachers/students/admin)
        // 2. Courses (which may reference users as instructors)
        // 3. Enrollments (which reference users and courses)

        $this->call(UserSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(EnrollmentSeeder::class);
    }
}
