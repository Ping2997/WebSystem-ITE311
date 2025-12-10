<!-- Add Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php $studentValidation = session('student_validation') instanceof \CodeIgniter\Validation\Validation ? session('student_validation') : null; ?>

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

        <form action="<?= base_url('students/store') ?>" method="post">
          <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control<?= $studentValidation && $studentValidation->hasError('username') ? ' is-invalid' : '' ?>" value="<?= old('username') ?>" required>
            <?php if ($studentValidation && $studentValidation->hasError('username')): ?>
              <div class="invalid-feedback">
                <?= esc($studentValidation->getError('username')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control<?= $studentValidation && $studentValidation->hasError('email') ? ' is-invalid' : '' ?>" value="<?= old('email') ?>" required>
            <?php if ($studentValidation && $studentValidation->hasError('email')): ?>
              <div class="invalid-feedback">
                <?= esc($studentValidation->getError('email')) ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control<?= $studentValidation && $studentValidation->hasError('password') ? ' is-invalid' : '' ?>" required>
            <?php if ($studentValidation && $studentValidation->hasError('password')): ?>
              <div class="invalid-feedback">
                <?= esc($studentValidation->getError('password')) ?>
              </div>
            <?php endif; ?>
            <small class="form-text text-muted">Minimum 6 characters</small>
          </div>

          <div class="mb-3">
            <?php
              $selected = old('year_level');
              echo view('partials/year_level_select', [
                  'selected'   => $selected,
                  'validation' => $studentValidation,
                  'field'      => 'year_level',
                  'label'      => 'Year Level',
              ]);
            ?>
          </div>

          <div class="mt-3 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-green">Save Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


