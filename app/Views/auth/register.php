<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>

  <?= $this->include('tempates/header') ?>

  <!-- Register Content -->
  <main class="container auth-container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-7 col-lg-6">
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

          <form action="<?= base_url('register') ?>" method="post" class="mt-2">
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
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>