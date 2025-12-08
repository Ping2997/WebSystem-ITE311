<div class="container py-4">
  <h3 class="mb-3 text-green">Teacher Dashboard</h3>
  <p>Welcome, <?= esc($name) ?>!</p>

  <div class="mb-3">
    <form class="row g-2 align-items-center" onsubmit="const cid=this.querySelector('select[name=course]').value; if(cid){ window.location.href='<?= base_url('admin/course') ?>/'+cid+'/upload'; } return false;">
      <div class="col-auto">
        <label class="col-form-label fw-semibold">Upload to course:</label>
      </div>
      <div class="col-auto">
        <select name="course" class="form-select form-select-sm" required>
          <option value="" selected disabled>Select course</option>
          <?php if (!empty($courses)): ?>
          <?php foreach ($courses as $c): ?>
            <option value="<?= (int)$c['id'] ?>"><?= esc($c['title']) ?></option>
          <?php endforeach; ?>
          <?php else: ?>
            <option value="" disabled>(No courses available)</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-green btn-sm" type="submit"><i class="fa fa-upload me-1"></i> Go to Upload</button>
      </div>
      <div class="col-auto">
        <a href="#" class="btn btn-outline-green btn-sm" onclick="const sel=this.closest('form').querySelector('select[name=course]'); if(sel && sel.value){ window.location.href='<?= base_url('admin/course') ?>/'+sel.value; } return false;">
          <i class="fa fa-info-circle me-1"></i> View Details
        </a>
      </div>
    </form>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">My Courses</div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <?php if (!empty($courses)): ?>
          <?php foreach ($courses as $c): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold mb-0"><?= esc($c['title']) ?></div>
              </div>
              <div class="btn-group btn-group-sm" role="group">
                <a href="<?= base_url('admin/course/' . (int)$c['id']) ?>" class="btn btn-outline-green">
                  <i class="fa fa-info-circle me-1"></i> Details
                </a>
                <a href="<?= base_url('admin/course/' . (int)$c['id'] . '/upload') ?>" class="btn btn-outline-success">
                  <i class="fa fa-upload me-1"></i> Upload
                </a>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="list-group-item text-muted">No courses yet.</li>
        <?php endif; ?>
      </ul>
      <button type="button" class="btn btn-green btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#subjectModal">Create New Course</button>
      <button type="button" class="btn btn-green btn-sm mt-3 ms-2" data-bs-toggle="modal" data-bs-target="#studentModal">
        <i class="fa fa-user-plus me-1"></i> Add Student
      </button>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">New Submissions</div>
    <div class="card-body">
      <p class="text-muted mb-0">No new submissions.</p>
    </div>
  </div>

  <?= view('courses/modal_form') ?>
  <?= view('Admin/modal_form') ?>
</div>
