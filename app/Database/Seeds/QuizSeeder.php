<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        // Insert sample quizzes
        $quizzes = [
            [
                'lesson_id' => 1, // HTML Basics
                'title' => 'HTML Fundamentals Quiz',
                'description' => 'Test your knowledge of HTML basics',
                'time_limit' => 30,
                'passing_score' => 70.00,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 2, // CSS Styling
                'title' => 'CSS Styling Quiz',
                'description' => 'Test your CSS knowledge',
                'time_limit' => 45,
                'passing_score' => 75.00,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lesson_id' => 3, // JavaScript Fundamentals
                'title' => 'JavaScript Basics Quiz',
                'description' => 'Test your JavaScript fundamentals',
                'time_limit' => 60,
                'passing_score' => 80.00,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('quizzes')->insertBatch($quizzes);

        // Insert sample submissions
        $submissions = [
            [
                'student_id' => 4, // student1
                'quiz_id' => 1,
                'score' => 85.00,
                'answers' => json_encode(['q1' => 'a', 'q2' => 'b', 'q3' => 'c']),
                'time_taken' => 25,
                'status' => 'graded',
                'submitted_at' => date('Y-m-d H:i:s'),
                'graded_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 5, // student2
                'quiz_id' => 1,
                'score' => 90.00,
                'answers' => json_encode(['q1' => 'a', 'q2' => 'b', 'q3' => 'c']),
                'time_taken' => 20,
                'status' => 'graded',
                'submitted_at' => date('Y-m-d H:i:s'),
                'graded_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 4, // student1
                'quiz_id' => 2,
                'score' => 75.00,
                'answers' => json_encode(['q1' => 'a', 'q2' => 'b']),
                'time_taken' => 35,
                'status' => 'graded',
                'submitted_at' => date('Y-m-d H:i:s'),
                'graded_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('submissions')->insertBatch($submissions);
    }
}
