<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if (!$this->validate($rules)) {
                return view('auth/register', [
                    'validation' => $this->validator,
                    'title' => 'Register',
                ]);
            }

            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $builder = $this->db->table('users');
            if ($builder->insert($userData)) {
                session()->setFlashdata('success', 'Registration successful! Please login.');
                return redirect()->to('/login');
            }

            session()->setFlashdata('error', 'Registration failed. Please try again.');
        }

        return view('auth/register', [
            'validation' => $this->validator ?? null,
            'title' => 'Register',
        ]);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                    'title' => 'Login',
                ]);
            }

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $builder = $this->db->table('users');
            $user = $builder->where('username', $username)->get()->getRowArray();

            if ($user && password_verify($password, $user['password'])) {
                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'] ?? null,
                    'role' => $user['role'] ?? 'student',
                    'isLoggedIn' => true,
                ];
                session()->set($sessionData);
                session()->setFlashdata('success', 'Welcome back!');
                return redirect()->to('/dashboard');
            }

            session()->setFlashdata('error', 'Invalid username or password.');
        }

        return view('auth/login', [
            'validation' => $this->validator ?? null,
            'title' => 'Login',
        ]);
    }

    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('/');
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to('/login');
        }

        $userData = [
            'username' => session()->get('username'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];

        return view('auth/dashboard', [
            'user' => $userData,
            'title' => 'Dashboard',
        ]);
    }
}