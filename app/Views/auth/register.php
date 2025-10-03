<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .register-box {
      background: #ffffff;
      border-radius: 10px;
      padding: 32px;
      margin-top: 60px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .btn-green { background-color: var(--green-600); border-color: var(--green-600); color: #fff; }
    .btn-green:hover { background-color: var(--green-700); border-color: var(--green-700); color: #fff; }
    .login-link { text-align: center; margin-top: 15px; }
  </style>
</head>
<body>

  <!-- Top Navigation -->
  <nav class="navbar navbar-expand-lg theme-navbar">
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
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Log in</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Register Content -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="register-box">
          <h2 class="text-center mb-4 text-green">Create Account</h2>

          <?php if(session()->getFlashdata('success')): ?>
              <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>

          <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>
          <?php if(isset($validation)): ?>
              <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
          <?php endif; ?>

          <form action="<?= base_url('register') ?>" method="post">
              <div class="mb-3">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?= old('username') ?>">
              </div>
              <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label>Password</label>
                      <input type="password" name="password" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                      <label>Confirm Password</label>
                      <input type="password" name="password_confirm" class="form-control">
                  </div>
              </div>
              <button type="submit" class="btn btn-green w-100">Create Account</button>
          </form>

          <div class="login-link">
            <p class="mt-3">Already have an account? <a href="<?= base_url('login') ?>">Login here</a></p>
          </div>
        </div> 
      </div> 
    </div> 
  </div> 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>