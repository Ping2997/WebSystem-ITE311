<?php

namespace App\Controllers;

class Admin extends BaseController
{ 
    public function dashboard()
    {
        // Must be logged in
        if  (!session () ->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please Login first.');
            return redirect()->to(base_url('login'));
        }

        // Must be admin
        if (session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        // Prepare courses for dashboard (moved from view)
        $courses = db_connect()->table('courses')
            ->select('id, title')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Render unified wrapper with user context and courses
        return view('Admin/admin', [
            'user' => [
                'name'  => session('name'),
                'email' => session('email'),
                'role'  => session('role'),
            ],
            'courses' => $courses,
        ]);
        }
}