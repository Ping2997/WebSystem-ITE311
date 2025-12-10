<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'first_name', 'last_name', 'department', 'role', 'status', 'year_level', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = false; // We'll handle soft deletes manually

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Get all users including deleted (for archive)
     */
    public function getAllUsersWithDeleted(): array
    {
        return $this->withDeleted()->findAll();
    }
    
    /**
     * Get only deleted users
     */
    public function getDeletedUsers(): array
    {
        $db = db_connect();
        return $db->table('users')
            ->select('id, username, email, role, status, year_level, created_at, deleted_at')
            ->where('deleted_at IS NOT NULL', null, false)
            ->orderBy('deleted_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Soft delete user
     */
    public function softDelete($id): bool
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Restore deleted user
     */
    public function restore($id): bool
    {
        return $this->update($id, ['deleted_at' => null]);
    }
    
    /**
     * Permanently delete users deleted more than 30 days ago
     */
    public function cleanupOldDeletes(): int
    {
        $db = db_connect();
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $deleted = $db->table('users')
            ->where('deleted_at IS NOT NULL', null, false)
            ->where('deleted_at <', $thirtyDaysAgo)
            ->get()
            ->getResultArray();
        
        $count = 0;
        foreach ($deleted as $user) {
            if ($db->table('users')->delete(['id' => $user['id']])) {
                $count++;
            }
        }
        return $count;
    }
}
