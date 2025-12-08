<?php
// Reusable year-level select field.
// Expected vars:
// - $selected   (string|null) current/old value
// - $validation (optional) CodeIgniter validation object
// - $field      (string) field name, defaults to 'year_level'
// - $label      (string) label text, defaults to 'Year Level'

$field = $field ?? 'year_level';
$label = $label ?? 'Year Level';
?>
<label class="form-label fw-semibold"><?= esc($label) ?></label>
<select name="<?= esc($field) ?>" class="form-select<?= isset($validation) && $validation->hasError($field) ? ' is-invalid' : '' ?>" required>
  <option value="" disabled <?= $selected ? '' : 'selected' ?>>Select year level</option>
  <option value="1st Year" <?= $selected === '1st Year' ? 'selected' : '' ?>>1st Year</option>
  <option value="2nd Year" <?= $selected === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
  <option value="3rd Year" <?= $selected === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
  <option value="4th Year" <?= $selected === '4th Year' ? 'selected' : '' ?>>4th Year</option>
</select>
<?php if (isset($validation) && $validation->hasError($field)): ?>
  <div class="invalid-feedback d-block">
    <?= esc($validation->getError($field)) ?>
  </div>
<?php endif; ?>
