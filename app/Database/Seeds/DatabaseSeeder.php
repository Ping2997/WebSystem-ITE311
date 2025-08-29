<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in order to maintain referential integrity
        $this->call('UserSeeder');
        $this->call('CourseSeeder');
        $this->call('QuizSeeder');
        
        echo "Database seeding completed successfully!\n";
    }
}

