    <nav class="navbar navbar-expand-lg navbar-dark theme-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="fas fa-graduation-cap me-2"></i>ITE311 LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                    // Normalize current request path regardless of base URL or public/ prefix
                    $path = trim(parse_url(current_url(), PHP_URL_PATH) ?? '', '/');
                    $role = strtolower((string) (session('role') ?? ''));
                    $dashUrl = base_url('dashboard');
                    if ($role === 'admin') {
                        $dashUrl = base_url('admin/dashboard');
                    } elseif ($role === 'teacher') {
                        $dashUrl = base_url('teacher/dashboard');
                    } elseif ($role === 'student') {
                        // Students primarily use announcements as landing
                        $dashUrl = base_url('announcements');
                    }
                    // Hide Dashboard when already on any dashboard/announcements URL
                    $isOnDashboard = (
                        $path === 'dashboard'
                        || str_contains($path, '/admin/dashboard')
                        || str_contains($path, '/teacher/dashboard')
                        || str_contains($path, '/announcements')
                    );
                ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item"><span class="navbar-text me-3"><?= esc(session('name')) ?> (<?= esc(session('role')) ?>)</span></li>
                        <?php if (!$isOnDashboard): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= $dashUrl ?>">Dashboard</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>