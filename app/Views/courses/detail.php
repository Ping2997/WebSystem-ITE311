<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0 text-green">Course Details</h3>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-green btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
      </a>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-1"><?= esc($course['title'] ?? 'Course') ?></h5>
            <p class="text-muted mb-2">Instructor: <?= esc($course['instructor_name'] ?? 'N/A') ?></p>
            <?php if (!empty($course['description'])): ?>
              <p class="mb-2"><?= esc($course['description']) ?></p>
            <?php endif; ?>
            <dl class="row mb-0 small">
              <dt class="col-sm-4">Semester</dt>
              <dd class="col-sm-8"><?= esc($course['semester'] ?? '—') ?></dd>
              <dt class="col-sm-4">Year Level</dt>
              <dd class="col-sm-8"><?= esc($course['year_level'] ?? '—') ?></dd>
              <dt class="col-sm-4">Capacity</dt>
              <dd class="col-sm-8">
                <?php
                  $cap = isset($course['capacity']) ? (int) $course['capacity'] : 0;
                  $enrolledCnt = isset($enrolledCount) ? (int) $enrolledCount : 0;
                  if ($cap > 0) {
                    echo esc($enrolledCnt) . ' / ' . esc($cap);
                  } else {
                    echo esc($enrolledCnt) . ' enrolled (no limit)';
                  }
                ?>
              </dd>
              <dt class="col-sm-4">Schedule</dt>
              <dd class="col-sm-8">
                <?php
                  $sd = $course['start_date'] ?? '';
                  $ed = $course['end_date'] ?? '';
                  $st = $course['start_time'] ?? '';
                  $et = $course['end_time'] ?? '';
                  $sdFmt = $sd ? date('M j, Y', strtotime($sd)) : '';
                  $edFmt = $ed ? date('M j, Y', strtotime($ed)) : '';
                  $stFmt = $st ? date('g:i A', strtotime($st)) : '';
                  $etFmt = $et ? date('g:i A', strtotime($et)) : '';
                ?>
                <?php if ($sdFmt || $edFmt): ?>
                  <div>Date: <?= esc($sdFmt) ?><?= ($sdFmt && $edFmt) ? ' – ' : '' ?><?= esc($edFmt) ?></div>
                <?php endif; ?>
                <?php if ($stFmt || $etFmt): ?>
                  <div>Time: <?= esc($stFmt) ?><?= ($stFmt && $etFmt) ? ' – ' : '' ?><?= esc($etFmt) ?></div>
                <?php endif; ?>
                <?php if (!$sdFmt && !$edFmt && !$stFmt && !$etFmt): ?>
                  <div>—</div>
                <?php endif; ?>
              </dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card shadow-sm h-100">
          <div class="card-header fw-semibold">Enrolled Students</div>
          <div class="card-body p-0">
            <?php if (!empty($enrolledStudents)): ?>
              <div class="table-responsive">
                <table class="table mb-0 table-sm align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Year</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; foreach ($enrolledStudents as $s): ?>
                      <tr>
                        <td><?= $i++ ?></td>
                        <td><?= esc($s['username']) ?></td>
                        <td><?= esc($s['email']) ?></td>
                        <td><?= esc($s['year_level'] ?? '—') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted px-3 py-2 mb-0">No students enrolled yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
