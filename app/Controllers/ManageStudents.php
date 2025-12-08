<?php

namespace App\Controllers;

use App\Models\UserModel;

class ManageStudents extends BaseController
{
    public function store()
    {
        if (!session()->get('isLoggedIn') || !in_array(session('role'), ['admin', 'teacher'], true)) {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[2]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'year_level' => 'required',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            // Redirect back to dashboard so the Add Student modal can show errors
            return redirect()->back()->withInput()->with('student_validation', $validation);
        }

        $userModel = new UserModel();
        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role'       => 'student',
            'status'     => 'active',
            'year_level' => $this->request->getPost('year_level'),
        ];

        if ($userModel->insert($data)) {
            session()->setFlashdata('success', 'Student created successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to create student.');
        }

        return redirect()->to(base_url('dashboard'));
    }
}
