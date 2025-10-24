<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Materials</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

  <main class="container py-5">
    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><i class="fa fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
    <?php elseif(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('debug')): ?>
      <div class="alert alert-warning small mb-3"><strong>Debug:</strong> <?= esc(session()->getFlashdata('debug')) ?></div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0 text-green"><i class="fa fa-folder-open me-2"></i>Available Materials</h4>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-green btn-sm"><i class="fa fa-arrow-left me-1"></i>Back to Dashboard</a>
    </div>
    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:55%">File Name</th>
                <th style="width:20%">Uploaded</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($materials)): ?>
                <?php foreach($materials as $m): ?>
                  <tr>
                    <td class="align-middle"><i class="fa fa-file me-2 text-green"></i><?= esc($m['file_name']) ?></td>
                    <td class="align-middle text-muted"><?= isset($m['created_at']) ? date('M d, Y h:i A', strtotime($m['created_at'])) : '' ?></td>
                    <td class="align-middle">
                      <a href="<?= base_url('materials/download/'.$m['id']) ?>" class="btn btn-green btn-sm me-1">
                        <i class="fa fa-download me-1"></i>Download
                      </a>
                      <?php if (in_array(session('role'), ['admin','teacher'], true)) : ?>
                        <a href="<?= base_url('materials/delete/'.$m['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this material?');">
                          <i class="fa fa-trash me-1"></i>Delete
                        </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-muted p-4">No materials available.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>