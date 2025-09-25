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
        // Gate: On any GET enforce either a valid gate token or a homepage referrer
        if ($this->request->getMethod() === 'get') {
            $provided = $this->request->getGet('gate');
            $ref = $this->request->getHeaderLine('Referer');
            $home = rtrim(base_url('/'), '/');

            // Allow only if gate is present and (matches session when available), OR referer is the homepage
            $expected = session()->get('home_gate');
            $gateValid = $provided && (!$expected || hash_equals($expected, $provided));
            $fromHome  = $ref && (stripos($ref, $home) === 0);

            if (!($gateValid || $fromHome)) {
                return redirect()->to('/');
            }
        }

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
                    'title' => 'Register',
                    'gate'  => $this->request->getGet('gate') ?? '',
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
                // Redirect to home; login page requires gate from home
                return redirect()->to('/');
            } else {
                session()->setFlashdata('error', 'Registration failed. Please try again.');
            }
        }

        return view('auth/register', [
            'validation' => $this->validator ?? null,
            'title' => 'Register',
            'gate'  => $this->request->getGet('gate') ?? '',
        ]);
    }

    public function login()
    {
        // Gate: On any GET without a gate param, redirect to homepage immediately
        if ($this->request->getMethod() === 'get') {
            $provided = $this->request->getGet('gate');
            if (!$provided) {
                return redirect()->to('/');
            }
            // Optional: also verify against session token when present
            $expected = session()->get('home_gate');
            if ($expected && !hash_equals($expected, $provided)) {
                return redirect()->to('/');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                    'title' => 'Login',
                    'gate'  => $this->request->getGet('gate') ?? '',
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
            'title' => 'Login',
            'gate'  => $this->request->getGet('gate') ?? '',
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
