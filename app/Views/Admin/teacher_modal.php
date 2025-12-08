<!-- Add Teacher Modal -->
<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="teacherModalLabel">Add Teacher</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php $teacherValidation = session('teacher_validation') instanceof \CodeIgniter\Validation\Validation ? session('teacher_validation') : null; ?>

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

        <form action="<?= base_url('teachers/store') ?>" method="post">
          <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control<?= $teacherValidation && $teacherValidation->hasError('username') ? ' is-invalid' : '' ?>" value="<?= old('username') ?>" required>
            <?php if ($teacherValidation && $teacherValidation->hasError('username')): ?>
              <div class="invalid-feedback">
                <?= esc($teacherValidation->getError('username')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control<?= $teacherValidation && $teacherValidation->hasError('email') ? ' is-invalid' : '' ?>" value="<?= old('email') ?>" required>
            <?php if ($teacherValidation && $teacherValidation->hasError('email')): ?>
              <div class="invalid-feedback">
                <?= esc($teacherValidation->getError('email')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control<?= $teacherValidation && $teacherValidation->hasError('password') ? ' is-invalid' : '' ?>" required>
            <?php if ($teacherValidation && $teacherValidation->hasError('password')): ?>
              <div class="invalid-feedback">
                <?= esc($teacherValidation->getError('password')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">First Name</label>
            <input type="text" name="first_name" class="form-control<?= $teacherValidation && $teacherValidation->hasError('first_name') ? ' is-invalid' : '' ?>" value="<?= old('first_name') ?>">
            <?php if ($teacherValidation && $teacherValidation->hasError('first_name')): ?>
              <div class="invalid-feedback">
                <?= esc($teacherValidation->getError('first_name')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Last Name</label>
            <input type="text" name="last_name" class="form-control<?= $teacherValidation && $teacherValidation->hasError('last_name') ? ' is-invalid' : '' ?>" value="<?= old('last_name') ?>">
            <?php if ($teacherValidation && $teacherValidation->hasError('last_name')): ?>
              <div class="invalid-feedback">
                <?= esc($teacherValidation->getError('last_name')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Department</label>
            <select name="department" class="form-select<?= $teacherValidation && $teacherValidation->hasError('department') ? ' is-invalid' : '' ?>" required>
              <option value="" disabled <?= old('department') ? '' : 'selected' ?>>Select department</option>
              <option value="Computer Science" <?= old('department') === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
              <option value="Mathematics" <?= old('department') === 'Mathematics' ? 'selected' : '' ?>>Mathematics</option>
              <option value="English" <?= old('department') === 'English' ? 'selected' : '' ?>>English</option>
              <option value="Science" <?= old('department') === 'Science' ? 'selected' : '' ?>>Science</option>
              <option value="Others" <?= old('department') === 'Others' ? 'selected' : '' ?>>Others</option>
            </select>
            <?php if ($teacherValidation && $teacherValidation->hasError('department')): ?>
              <div class="invalid-feedback d-block">
                <?= esc($teacherValidation->getError('department')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mt-3 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-green">Save Teacher</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
