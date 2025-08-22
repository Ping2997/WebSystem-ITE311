<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Insert sample courses
        $courses = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the basics of HTML, CSS, and JavaScript for web development.',
                'instructor_id' => 2, // instructor1
                'category' => 'Programming',
                'level' => 'beginner',
                'duration' => 480, // 8 hours
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Master PHP programming with advanced concepts and best practices.',
                'instructor_id' => 3, // instructor2
                'category' => 'Programming',
                'level' => 'advanced',
                'duration' => 600, // 10 hours
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design Fundamentals',
                'description' => 'Learn database design principles and SQL fundamentals.',
                'instructor_id' => 2, // instructor1
                'category' => 'Database',
                'level' => 'intermediate',
                'duration' => 360, // 6 hours
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('courses')->insertBatch($courses);

        // Insert sample lessons
        $lessons = [
            [
                'course_id' => 1,
                'title' => 'HTML Basics',
                'content' => 'Introduction to HTML tags, elements, and document structure.',
                'lesson_order' => 1,
                'duration' => 60,
                'video_url' => null,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'CSS Styling',
                'content' => 'Learn CSS selectors, properties, and styling techniques.',
                'lesson_order' => 2,
                'duration' => 90,
                'video_url' => null,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 1,
                'title' => 'JavaScript Fundamentals',
                'content' => 'Introduction to JavaScript programming basics.',
                'lesson_order' => 3,
                'duration' => 120,
                'video_url' => null,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP OOP Concepts',
                'content' => 'Object-oriented programming in PHP.',
                'lesson_order' => 1,
                'duration' => 120,
                'video_url' => null,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 3,
                'title' => 'Database Normalization',
                'content' => 'Understanding database normalization forms.',
                'lesson_order' => 1,
                'duration' => 90,
                'video_url' => null,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('lessons')->insertBatch($lessons);

        // Insert sample enrollments
        $enrollments = [
            [
                'student_id' => 4, // student1
                'course_id' => 1,
                'enrollment_date' => date('Y-m-d H:i:s'),
                'completion_date' => null,
                'progress' => 75.00,
                'status' => 'in_progress',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 5, // student2
                'course_id' => 1,
                'enrollment_date' => date('Y-m-d H:i:s'),
                'completion_date' => date('Y-m-d H:i:s'),
                'progress' => 100.00,
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'student_id' => 6, // student3
                'course_id' => 2,
                'enrollment_date' => date('Y-m-d H:i:s'),
                'completion_date' => null,
                'progress' => 25.00,
                'status' => 'in_progress',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('enrollments')->insertBatch($enrollments);
    }
}
