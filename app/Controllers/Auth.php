<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function register()
    {
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Set validation rules
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => [
                    'rules'  => 'required|min_length[2]|max_length[100]|is_unique[users.username]',
                    'errors' => [
                        'required'   => 'Username is required',
                        'min_length' => 'Username must be at least 2 characters long',
                        'max_length' => 'Username cannot exceed 100 characters',
                        'is_unique'  => 'This username is already taken'
                    ]
                ],
                'email' => [
                    'rules'  => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required'   => 'Email is required',
                        'valid_email'=> 'Please enter a valid email address',
                        'is_unique'  => 'This email is already registered'
                    ]
                ],
                'password' => [
                    'rules'  => 'required|min_length[6]',
                    'errors' => [
                        'required'   => 'Password is required',
                        'min_length' => 'Password must be at least 6 characters long'
                    ]
                ],
                'password_confirm' => [
                    'rules'  => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Password confirmation is required',
                        'matches'  => 'Password confirmation does not match'
                    ]
                ]
            ]);

            if ($validation->withRequest($this->request)->run()) {
                // Use the UserModel so hashing happens via model callbacks
                $userModel = new \App\Models\UserModel();

                // Prepare user data aligned with schema
                $userData = [
                    'username' => $this->request->getPost('username'),
                    'email'    => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password'), // will be hashed by model
                    'role'     => 'student',
                    'status'   => 'active',
                ];

                if ($userModel->insert($userData)) {
                    // Set success flash message
                    session()->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to(base_url('login'));
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                // Validation failed
                session()->setFlashdata('error', 'Please fix the errors below.');
                return view('auth/register', ['validation' => $validation]);
            }
        }

        // GET: must come from homepage very recently (one-time)
        if (!session()->get('from_home')) {
            return redirect()->to(base_url('/'));
        }
        $fromTime = (int) (session()->get('from_home_time') ?? 0);
        if ($fromTime === 0 || (time() - $fromTime) > 15) {
            session()->remove('from_home');
            session()->remove('from_home_time');
            return redirect()->to(base_url('/'));
        }
        // consume flags so direct reload won't work
        session()->remove('from_home');
        session()->remove('from_home_time');
        return view('auth/register');
    }

    public function login()
    {
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Set validation rules
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Username or Email is required',
                    ]
                ],
                'password' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Password is required'
                    ]
                ]
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $identifier = trim((string) $this->request->getPost('username'));
                $password   = (string) $this->request->getPost('password');

                // Find by username OR email
                $userModel = new \App\Models\UserModel();
                $user = $userModel
                    ->groupStart()
                        ->where('username', $identifier)
                        ->orWhere('email', $identifier)
                    ->groupEnd()
                    ->first();

                if ($user && ($user['status'] ?? 'active') === 'active' && password_verify($password, $user['password'])) {
                    // Create user session
                    $sessionData = [
                        'userID' => $user['id'],
                        'name' => $user['username'], // keep compatibility with views expecting name
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => true
                    ];
                    session()->set($sessionData);

                    // Regenerate session ID to prevent session fixation
                    session()->regenerate();

                    // Single dashboard route (view handles role-specific UI)
                    return redirect()->to(base_url('dashboard'));
                } else {
                    session()->setFlashdata('error', 'Invalid username/email or password, or account is inactive.');
                }
            } else {
                session()->setFlashdata('error', 'Please fix the errors below.');
                return view('auth/login', ['validation' => $validation]);
            }
        }

        // GET: must come from homepage very recently (one-time)
        if (!session()->get('from_home')) {
            return redirect()->to(base_url('/'));
        }
        $fromTime = (int) (session()->get('from_home_time') ?? 0);
        if ($fromTime === 0 || (time() - $fromTime) > 15) {
            session()->remove('from_home');
            session()->remove('from_home_time');
            return redirect()->to(base_url('/'));
        }
        // consume flags so direct reload won't work
        session()->remove('from_home');
        session()->remove('from_home_time');
        return view('auth/login');
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Set logout message and redirect
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('/'));
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        // Base user data
        $data = [
            'user' => [
                'name' => session()->get('name'),
                'role' => session()->get('role'),
            ]
        ];

        // If student, load courses for the dashboard
        if (session()->get('role') === 'student') {
            $userId = (int) (session()->get('userID') ?? 0);
            $enrollModel = new \App\Models\EnrollmentModel();

            $enrolledCourses = $enrollModel->getUserEnrollmentsDetailed($userId);

            $firstSemCourses = [];
            $secondSemCourses = [];

            foreach ($enrolledCourses as $course) {
                $sem = (string) ($course['semester'] ?? '');
                if ($sem === '2nd') {
                    $secondSemCourses[] = $course;
                } else {
                    $firstSemCourses[] = $course;
                }
            }

            usort($firstSemCourses, function ($a, $b) {
                return strcmp((string) ($a['start_date'] ?? ''), (string) ($b['start_date'] ?? ''));
            });

            usort($secondSemCourses, function ($a, $b) {
                return strcmp((string) ($a['start_date'] ?? ''), (string) ($b['start_date'] ?? ''));
            });

            $data['enrolledCourses'] = $enrolledCourses;
            $data['availableCourses'] = $enrollModel->getAvailableCourses($userId);
            $data['enrolledFirstSem'] = $firstSemCourses;
            $data['enrolledSecondSem'] = $secondSemCourses;
        }

        // If admin or teacher, provide list of courses for dashboards
        if (in_array(session()->get('role'), ['admin','teacher'], true)) {
            $data['courses'] = db_connect()->table('courses')
                ->select('id, title')
                ->orderBy('title', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('auth/dashboard', $data);
    }

    /**
     * Return user if they exist by username or email.
     * GET /auth/getUser?username=... OR ?email=...
     */
    public function getUser()
    {
        $username = $this->request->getGet('username');
        $email    = $this->request->getGet('email');

        if (!$username && !$email) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Provide username or email']);
        }

        $userModel = new \App\Models\UserModel();
        if ($username) {
            $user = $userModel->where('username', $username)->first();
        } else {
            $user = $userModel->where('email', $email)->first();
        }

        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['found' => false]);
        }

        // Do not expose password hash
        unset($user['password']);

        return $this->response->setJSON([
            'found' => true,
            'user'  => $user,
        ]);
    }
}