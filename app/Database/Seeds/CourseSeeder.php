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
            [
                'title'       => 'Web Development 101',
                'description' => 'Introductory course on HTML, CSS, and PHP.',
                'instructor_id' => 1,
                'category'    => 'Web Development',
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Database Management Systems',
                'description' => 'Learn the fundamentals of databases, SQL queries, and relational design.',
                'instructor_id' => 1,
                'category'    => 'Database',
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Object-Oriented Programming in PHP',
                'description' => 'Understand the principles of OOP including classes, inheritance, and polymorphism using PHP.',
                'instructor_id' => 1,
                'category'    => 'Programming',
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
        ],

        ];

        // Insert data
        $this->db->table('courses')->insertBatch($data);
    }
}
