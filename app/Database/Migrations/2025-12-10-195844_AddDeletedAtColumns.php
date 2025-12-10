<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtColumns extends Migration
{
    public function up()
    {
        $tables = ['users', 'courses', 'enrollments'];
        
        foreach ($tables as $table) {
            // Check if column already exists
            $fields = $this->db->getFieldData($table);
            $hasDeletedAt = false;
            
            foreach ($fields as $field) {
                if ($field->name === 'deleted_at') {
                    $hasDeletedAt = true;
                    break;
                }
            }
            
            if (!$hasDeletedAt) {
                // Add deleted_at column
                $column = [
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'default' => null
                    ]
                ];
                
                // For users table, add after updated_at
                if ($table === 'users') {
                    $column['deleted_at']['after'] = 'updated_at';
                }
                
                $this->forge->addColumn($table, $column);
            }
        }
        
        // Add indexes for better performance (only if they don't exist)
        $indexQueries = [
            'users' => 'CREATE INDEX IF NOT EXISTS `idx_users_deleted_at` ON `users` (`deleted_at`)',
            'courses' => 'CREATE INDEX IF NOT EXISTS `idx_courses_deleted_at` ON `courses` (`deleted_at`)',
            'enrollments' => 'CREATE INDEX IF NOT EXISTS `idx_enrollments_deleted_at` ON `enrollments` (`deleted_at`)'
        ];
        
        foreach ($indexQueries as $table => $sql) {
            try {
                // MySQL doesn't support IF NOT EXISTS for indexes, so we check manually
                $indexes = $this->db->getIndexData($table);
                $indexExists = false;
                
                foreach ($indexes as $index) {
                    if ($index->name === 'idx_' . $table . '_deleted_at') {
                        $indexExists = true;
                        break;
                    }
                }
                
                if (!$indexExists) {
                    $this->db->query(str_replace(' IF NOT EXISTS', '', $sql));
                }
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
        }
    }

    public function down()
    {
        // Remove indexes
        $this->db->query('DROP INDEX `idx_users_deleted_at` ON `users`');
        $this->db->query('DROP INDEX `idx_courses_deleted_at` ON `courses`');
        $this->db->query('DROP INDEX `idx_enrollments_deleted_at` ON `enrollments`');
        
        // Remove deleted_at columns
        $this->forge->dropColumn('users', 'deleted_at');
        $this->forge->dropColumn('courses', 'deleted_at');
        $this->forge->dropColumn('enrollments', 'deleted_at');
    }
}
