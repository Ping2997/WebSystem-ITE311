<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

  <!-- Login Content -->
  <main class="container auth-container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-5">
        <div class="login-box">
          <h2 class="text-center mb-4 text-green">Login</h2>

          <?php if(session()->getFlashdata('success')): ?>
              <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>

          <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <?php if(isset($validation)): ?>
              <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
          <?php endif; ?>

          <form action="<?= base_url('login') ?>" method="post" class="mt-2">
              <div class="mb-3">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?= old('username') ?>">
              </div>
              <div class="mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control">
              </div>
              <button type="submit" class="btn btn-green w-100">Login</button>
          </form>

          <div class="register-link">
            <p class="mt-3">Don't have an account? <a href="<?= base_url('register') ?>">Register here</a></p>
          </div>

        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>