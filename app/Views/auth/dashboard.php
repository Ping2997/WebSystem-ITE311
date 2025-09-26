<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
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
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark theme-navbar">
    <div class="container">
      <a class="navbar-brand" href="<?= base_url() ?>">ITE311 LMS</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Dashboard</a></li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item"><span class="nav-link"><?= esc(session('name')) ?> (<?= esc(session('role')) ?>)</span></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php
      // Wrapper: load role-specific partials
      $role = $user['role'] ?? session('role');
      $name = $user['name'] ?? session('name');

      switch ($role) {
        case 'admin':
          echo view('tempates/admin', ['name' => $name]);
          break;
        case 'teacher':
          echo view('tempates/teacher', ['name' => $name]);
          break;
        case 'student':
          echo view('tempates/student', ['name' => $name]);
          break;
        default:
          echo '<div class="alert alert-warning mt-3">Role not recognized.</div>';
          break;
      }
    ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>