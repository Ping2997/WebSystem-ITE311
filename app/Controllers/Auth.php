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
        // Add debugging
        $method = $this->request->getMethod();
        $postData = $this->request->getPost();
        
        // Debug output
        echo "<!-- DEBUG: Method = $method, POST data = " . json_encode($postData) . " -->";
        
        if ($this->request->getMethod() === 'POST' && !empty($postData)) {
            // Set validation rules
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if (!$this->validate($rules)) {
                return view('auth/register', [
                    'validation' => $this->validator,
                    'title' => 'Register'
                ]);
            }

            // Hash the password
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            // Prepare user data
            $userData = [
                'username' => $this->request->getPost('username'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Save user to database
            $builder = $this->db->table('users');
            if ($builder->insert($userData)) {
                session()->setFlashdata('success', 'Registration successful! Please login.');
                return redirect()->to('/login');
            } else {
                session()->setFlashdata('error', 'Registration failed. Please try again.');
            }
        }

        return view('auth/register', [
            'validation' => $this->validator ?? null,
            'title' => 'Register'
        ]);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                    'title' => 'Login'
                ]);
            }

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Find user by username
            $builder = $this->db->table('users');
            $user = $builder->where('username', $username)->get()->getRowArray();

            if ($user && password_verify($password, $user['password'])) {
                // Set session data
                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'isLoggedIn' => true
                ];
                session()->set($sessionData);
                
                session()->setFlashdata('success', 'Welcome back!');
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Invalid username or password.');
            }
        }

        return view('auth/login', [
            'validation' => $this->validator ?? null,
            'title' => 'Login'
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
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to('/login');
        }

        $userData = [
            'username' => session()->get('username'),
            'first_name' => session()->get('first_name'),
            'last_name' => session()->get('last_name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];

        return view('auth/dashboard', [
            'user' => $userData,
            'title' => 'Dashboard'
        ]);
    }
}
