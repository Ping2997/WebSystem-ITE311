<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Search Results</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

  <div class="container py-4">
    <h3 class="mb-3 text-green">Course Search Results</h3>

    <form class="row g-2 mb-3" action="<?= base_url('courses/search') ?>" method="get">
      <div class="col-sm-6">
        <div class="input-group">
          <input type="text" class="form-control" name="search_term" placeholder="Search courses..." value="<?= esc($searchTerm ?? '') ?>">
          <button class="btn btn-outline-primary" type="submit">
            <i class="fa fa-search me-1"></i> Search
          </button>
        </div>
      </div>
    </form>

    <?php if (!empty($courses)): ?>
      <div class="row g-3">
        <?php foreach ($courses as $course): ?>
          <div class="col-md-6">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title mb-1"><?= esc($course['title']) ?></h5>
                <?php if (!empty($course['instructor_name'])): ?>
                  <p class="mb-1 small text-muted">Instructor: <?= esc($course['instructor_name']) ?></p>
                <?php endif; ?>
                <p class="card-text small mb-2"><?= esc($course['description']) ?></p>
                <?php
                  $st = $course['start_time'] ?? '';
                  $et = $course['end_time'] ?? '';
                  $stFmt = $st ? date('g:i A', strtotime($st)) : '';
                  $etFmt = $et ? date('g:i A', strtotime($et)) : '';
                ?>
                <?php if ($stFmt || $etFmt): ?>
                  <p class="mb-1 small text-muted">
                    Time: <?= esc($stFmt) ?>
                    <?php if ($stFmt && $etFmt): ?> – <?php endif; ?>
                    <?= esc($etFmt) ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">No courses matched your search.</p>
    <?php endif; ?>

    <div class="mt-3">
      <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-green btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
