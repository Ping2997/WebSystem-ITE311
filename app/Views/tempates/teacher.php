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
    </form>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">My Courses</div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">No courses yet.</li>
      </ul>
      <a href="#" class="btn btn-green btn-sm mt-3">Create New Course</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">New Submissions</div>
    <div class="card-body">
      <p class="text-muted mb-0">No new submissions.</p>
    </div>
  </div>
</div>
