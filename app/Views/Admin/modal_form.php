<!-- Add New Subject Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="subjectModalLabel">Add New Subject</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('courses/store') ?>" method="post">
          <div class="mb-3">
            <label class="form-label fw-semibold">Course Title</label>
            <input type="text" name="title" class="form-control<?= isset($validation) && $validation->hasError('title') ? ' is-invalid' : '' ?>" value="<?= old('title') ?>" required>
            <?php if (isset($validation) && $validation->hasError('title')): ?>
              <div class="invalid-feedback">
                <?= esc($validation->getError('title')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Capacity (max students)</label>
            <input type="number" min="1" name="capacity" class="form-control<?= isset($validation) && $validation->hasError('capacity') ? ' is-invalid' : '' ?>" value="<?= old('capacity') ?>" placeholder="e.g. 30">
            <?php if (isset($validation) && $validation->hasError('capacity')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($validation->getError('capacity')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Enter a short description of the course...">
<?= old('description') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Semester</label>
            <select name="semester" class="form-select<?= isset($validation) && $validation->hasError('semester') ? ' is-invalid' : '' ?>" required>
              <option value="" disabled <?= old('semester') ? '' : 'selected' ?>>Select semester</option>
              <option value="1st" <?= old('semester') === '1st' ? 'selected' : '' ?>>1st Semester</option>
              <option value="2nd" <?= old('semester') === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
            </select>
            <?php if (isset($validation) && $validation->hasError('semester')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($validation->getError('semester')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <?php
              $selected = old('year_level');
              echo view('partials/year_level_select', [
                  'selected'   => $selected,
                  'validation' => $validation ?? null,
                  'field'      => 'year_level',
                  'label'      => 'Year Level',
              ]);
            ?>
          </div>

          <?php if (session('role') === 'admin'): ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Assign Teacher</label>
            <select name="instructor_id" class="form-select<?= isset($validation) && $validation->hasError('instructor_id') ? ' is-invalid' : '' ?>" required>
              <option value="" disabled <?= old('instructor_id') ? '' : 'selected' ?>>Select a teacher</option>
              <?php
                $db = db_connect();
                $teachers = $db->table('users')
                    ->select('id, username, first_name, last_name, email')
                    ->where('role', 'teacher')
                    ->where('status', 'active')
                    ->orderBy('username', 'ASC')
                    ->get()
                    ->getResultArray();
                $selectedInstructor = old('instructor_id');
              ?>
              <?php if (!empty($teachers)): ?>
                <?php foreach ($teachers as $teacher): ?>
                  <?php
                    $teacherId = (int) $teacher['id'];
                    $teacherName = !empty($teacher['first_name']) || !empty($teacher['last_name']) 
                        ? trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? ''))
                        : $teacher['username'];
                    $isSelected = $selectedInstructor == $teacherId;
                  ?>
                  <option value="<?= $teacherId ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= esc($teacherName) ?> (<?= esc($teacher['username']) ?>)
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled>No teachers available</option>
              <?php endif; ?>
            </select>
            <?php if (isset($validation) && $validation->hasError('instructor_id')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($validation->getError('instructor_id')) ?>
              </div>
            <?php endif; ?>
            <small class="form-text text-muted">Select the teacher who will instruct this course.</small>
          </div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label fw-semibold">Start Date</label>
            <input type="date" name="start_date" class="form-control<?= isset($validation) && $validation->hasError('start_date') ? ' is-invalid' : '' ?>" value="<?= old('start_date') ?>">
            <?php if (isset($validation) && $validation->hasError('start_date')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($validation->getError('start_date')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">End Date</label>
            <input type="date" name="end_date" class="form-control<?= isset($validation) && $validation->hasError('end_date') ? ' is-invalid' : '' ?>" value="<?= old('end_date') ?>">
            <?php if (isset($validation) && $validation->hasError('end_date')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($validation->getError('end_date')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Start Time</label>
              <input type="time" name="start_time" class="form-control<?= isset($validation) && $validation->hasError('start_time') ? ' is-invalid' : '' ?>" value="<?= old('start_time') ?>">
              <?php if (isset($validation) && $validation->hasError('start_time')): ?>
                <div class="invalid-feedback d-block">
                  <?= esc($validation->getError('start_time')) ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">End Time</label>
              <input type="time" name="end_time" class="form-control<?= isset($validation) && $validation->hasError('end_time') ? ' is-invalid' : '' ?>" value="<?= old('end_time') ?>">
              <?php if (isset($validation) && $validation->hasError('end_time')): ?>
                <div class="invalid-feedback d-block">
                  <?= esc($validation->getError('end_time')) ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="mt-3 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-green">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
