<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

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
          echo view('Admin/admin', ['name' => $name, 'courses' => $courses ?? []]);
          break;
        case 'teacher':
          echo view('tempates/teacher', ['name' => $name, 'courses' => $courses ?? []]);
          break;
        case 'student':
          echo view('Student/student', [
            'name' => $name,
            'enrolledCourses' => $enrolledCourses ?? ($data['enrolledCourses'] ?? ($user['enrolledCourses'] ?? [])),
            'availableCourses' => $availableCourses ?? ($data['availableCourses'] ?? ($user['availableCourses'] ?? [])),
          ]);
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