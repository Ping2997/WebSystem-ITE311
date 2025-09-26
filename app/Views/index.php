<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-700: #2f6f65;
            --green-600: #3a7d6d;
            --green-500: #4f8f7f;
            --green-100: #eaf3f0;
        }
        body { background-color: var(--green-100); }
        .text-green { color: var(--green-600) !important; }
        .theme-navbar { background-color: var(--green-600); }
        .theme-navbar .navbar-brand,
        .theme-navbar .nav-link,
        .theme-navbar .navbar-toggler { color: #ffffff !important; }
        .btn-green { background-color: var(--green-600); border-color: var(--green-600); color: #fff; }
        .btn-green:hover { background-color: var(--green-700); border-color: var(--green-700); color: #fff; }
        .btn-outline-green { color: var(--green-600); border-color: var(--green-600); }
        .btn-outline-green:hover { background-color: var(--green-600); color: #fff; border-color: var(--green-600); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark theme-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-graduation-cap me-2"></i>ITE311 LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url() ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') . '?gate=' . urlencode($gate ?? '') ?>">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') . '?gate=' . urlencode($gate ?? '') ?>">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= session()->get('first_name') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 text-green mb-4">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Welcome to ITE311 Learning Management System
                </h1>
                <p class="lead text-muted mb-5">
                    A comprehensive platform for managing your educational journey. 
                    Access courses, track progress, and enhance your learning experience.
                </p>
                
                <?php if (!session()->get('isLoggedIn')): ?>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('register') ?>" class="btn btn-green btn-lg w-100">
                                <i class="fas fa-user-plus me-2"></i>Get Started
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-green btn-lg w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-green btn-lg w-100">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="text-center mb-5">Key Features</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x text-green mb-3"></i>
                        <h5 class="card-title">Course Management</h5>
                        <p class="card-text">Access a wide range of courses designed to enhance your skills and knowledge.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x text-green mb-3"></i>
                        <h5 class="card-title">Progress Tracking</h5>
                        <p class="card-text">Monitor your learning progress with detailed analytics and insights.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-question-circle fa-3x text-green mb-3"></i>
                        <h5 class="card-title">Interactive Quizzes</h5>
                        <p class="card-text">Test your knowledge with interactive quizzes and assessments.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

