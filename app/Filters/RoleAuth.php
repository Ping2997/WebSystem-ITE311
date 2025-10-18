<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Require login first
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        $role = strtolower((string) ($session->get('role') ?? ''));
        // Use public API to read the current path
        $path = trim($request->getUri()->getPath(), '/');

        // Admin section protection
        if (str_starts_with($path, 'admin')) {
            if ($role !== 'admin') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(base_url('announcements'));
            }
            return; // allowed
        }

        // Teacher section protection
        if (str_starts_with($path, 'teacher')) {
            if ($role !== 'teacher') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(base_url('announcements'));
            }
            return; // allowed
        }

        // Student section (future-proof): allow only students
        if (str_starts_with($path, 'student')) {
            if ($role !== 'student') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(base_url('announcements'));
            }
            return;
        }

        // Announcements are always allowed for logged-in users (especially students)
        if ($path === 'announcements') {
            return;
        }

        // Default: allow
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }
}
