<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome Back, Students!',
                'content' => 'Classes will officially start on October 21, 2025. Please check your schedules.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Maintenance Notice',
                'content' => 'The portal will be down for maintenance on October 25, 2025 from 1AM to 4AM.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
