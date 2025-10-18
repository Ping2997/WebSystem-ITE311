<?= $this->extend('tempates/template') ?>

<?= $this->section('content') ?>
  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <?php
    // Wrapper: load role-specific partials (these already extend the layout or are plain sections)
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
        echo view('tempates/student', [
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
<?= $this->endSection() ?>