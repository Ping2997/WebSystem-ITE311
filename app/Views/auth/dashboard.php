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

  <div class="container-fluid px-5 py-5">
    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php
      // Wrapper: load role-specific partials
      $role = $user['role'] ?? session('role');
      $name = $user['name'] ?? session('name');

      switch ($role) {
        case 'admin':
          echo view('Admin/admin', [
            'name' => $name, 
            'courses' => $courses ?? [],
            'totalUsers' => $totalUsers ?? 0,
            'totalCourses' => $totalCourses ?? 0,
            'activeStudents' => $activeStudents ?? 0,
            'totalEnrollments' => $totalEnrollments ?? 0,
            'allUsers' => $allUsers ?? [],
            'allCourses' => $allCourses ?? [],
            'allEnrollments' => $allEnrollments ?? [],
            'archivedUsers' => $archivedUsers ?? [],
            'archivedCourses' => $archivedCourses ?? [],
            'archivedEnrollments' => $archivedEnrollments ?? []
          ]);
          break;
        case 'teacher':
          echo view('tempates/teacher', [
            'name' => $name, 
            'courses' => $courses ?? [],
            'pendingEnrollments' => $pendingEnrollments ?? []
          ]);
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