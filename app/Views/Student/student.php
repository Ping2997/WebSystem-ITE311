<div class="container py-4">
  <h3 class="mb-3 text-muted text-green">Student Dashboard</h3>

  <p>Welcome, <?= esc($name) ?>!</p>

  <!-- Feedback message -->
  <div id="feedback" class="alert d-none" role="alert"></div>

  <!-- My Courses -->
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold bg-success text-white">1st Semester Courses</div>
        <div class="card-body">
          <ul id="first-sem-list" class="list-group list-group-flush">
            <?php if (!empty($enrolledFirstSem)): ?>
              <?php foreach ($enrolledFirstSem as $course): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div class="me-2">
                    <div class="d-flex align-items-center mb-1">
                      <span class="badge bg-success me-2">1st Sem</span>
                      <strong><?= esc($course['title']); ?></strong>
                    </div>
                    <small><?= esc($course['description']); ?></small><br>
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
                    <?php if ($sdFmt || $edFmt || $stFmt || $etFmt): ?>
                      <small class="text-muted d-block">
                        <?php if ($sdFmt || $edFmt): ?>
                          Date: <?= esc($sdFmt) ?>
                          <?php if ($sdFmt && $edFmt): ?> – <?php endif; ?>
                          <?= esc($edFmt) ?>
                        <?php endif; ?>
                        <?php if ($stFmt || $etFmt): ?>
                          <br>Time: <?= esc($stFmt) ?>
                          <?php if ($stFmt && $etFmt): ?> – <?php endif; ?>
                          <?= esc($etFmt) ?>
                        <?php endif; ?>
                      </small>
                    <?php endif; ?>
                  </div>
                  <div>
                    <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" class="btn btn-outline-success btn-sm">Materials</a>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-muted">No 1st semester courses yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold bg-success text-white">2nd Semester Courses</div>
        <div class="card-body">
          <ul id="second-sem-list" class="list-group list-group-flush">
            <?php if (!empty($enrolledSecondSem)): ?>
              <?php foreach ($enrolledSecondSem as $course): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div class="me-2">
                    <div class="d-flex align-items-center mb-1">
                      <span class="badge bg-success me-2">2nd Sem</span>
                      <strong><?= esc($course['title']); ?></strong>
                    </div>
                    <small><?= esc($course['description']); ?></small><br>
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
                    <?php if ($sdFmt || $edFmt || $stFmt || $etFmt): ?>
                      <small class="text-muted d-block">
                        <?php if ($sdFmt || $edFmt): ?>
                          Date: <?= esc($sdFmt) ?>
                          <?php if ($sdFmt && $edFmt): ?> – <?php endif; ?>
                          <?= esc($edFmt) ?>
                        <?php endif; ?>
                        <?php if ($stFmt || $etFmt): ?>
                          <br>Time: <?= esc($stFmt) ?>
                          <?php if ($stFmt && $etFmt): ?> – <?php endif; ?>
                          <?= esc($etFmt) ?>
                        <?php endif; ?>
                      </small>
                    <?php endif; ?>
                  </div>
                  <div>
                    <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" class="btn btn-outline-success btn-sm">Materials</a>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-muted">No 2nd semester courses yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <!-- Hidden list retained for JS compatibility when enrolling dynamically -->
    <ul id="my-courses-list" class="d-none">
      <?php if (empty($enrolledFirstSem) && empty($enrolledSecondSem)): ?>
        <li id="no-enrolled"></li>
      <?php endif; ?>
    </ul>

  <!-- Available Courses -->
  <div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold bg-success text-white">Available Courses</div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <form id="searchForm" class="d-flex" action="<?= base_url('courses/search') ?>" method="get">
            <div class="input-group">
              <input type="text" id="searchInput" class="form-control" placeholder="Search courses..." name="search_term">
              <button class="btn btn-outline-primary" type="submit">
                <i class="bi bi-search"></i> Search
              </button>
            </div>
          </form>
        </div>
      </div>
      <?php if (!empty($availableCourses)): ?>
        <div id="coursesContainer" class="row g-3">
          <?php foreach ($availableCourses as $course): ?>
            <div class="col-md-6">
              <div class="card h-100 course-card" data-card-course-id="<?= esc($course['id']); ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= esc($course['title']); ?></h5>
                  <p class="card-text"><?= esc($course['description']); ?></p>
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
                  <button class="btn btn-success enroll-btn"
                          data-course-id="<?= esc($course['id']); ?>">
                    Enroll
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted" id="no-available">No available courses right now.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  function bindEnrollButtons() {
    document.querySelectorAll('.enroll-btn').forEach(button => {
      if (button.dataset.bound === '1') return;
      button.dataset.bound = '1';
      button.addEventListener('click', function() {
        const courseId = this.getAttribute('data-course-id');
        const btn = this;

        fetch('<?= base_url('course/enroll') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCookie('csrf_cookie_name') || ''
          },
          body: JSON.stringify({ course_id: courseId })
        })
        .then(async res => {
          const ct = res.headers.get('content-type') || '';
          if (!ct.includes('application/json')) {
            const text = await res.text();
            throw new Error(text.replace(/<[^>]*>/g,'').trim() || 'Server returned non-JSON response');
          }
          return res.json();
        })
        .then(data => {
          showFeedback(data.success ? 'success' : 'danger', data.message || 'Request completed');
          if (data.success) {
            btn.disabled = true;
            btn.textContent = 'Enrolled';

            const card = btn.closest('[data-card-course-id]');
            if (card) {
              const gridCol = card.closest('.col-md-6');
              if (gridCol) gridCol.remove();
            }

            const c = data.course || {};
            let semester = String(c.semester || '').toLowerCase();
            if (!semester) {
              semester = '1st';
            }
            let targetList = null;
            let emptyPlaceholder = null;

            if (semester.startsWith('1')) {
              targetList = document.getElementById('first-sem-list');
              emptyPlaceholder = targetList ? targetList.querySelector('.text-muted') : null;
            } else if (semester.startsWith('2')) {
              targetList = document.getElementById('second-sem-list');
              emptyPlaceholder = targetList ? targetList.querySelector('.text-muted') : null;
            }

            if (targetList) {
              if (emptyPlaceholder) {
                emptyPlaceholder.remove();
              }

              const li = document.createElement('li');
              li.className = 'list-group-item d-flex justify-content-between align-items-start';
              const titleText = c.title || btn.closest('.card-body').querySelector('.card-title').textContent;
              const descText = c.description || btn.closest('.card-body').querySelector('.card-text').textContent;
              const startDate = c.start_date || '';
              const endDate = c.end_date || '';
              const startTime = c.start_time || '';
              const endTime = c.end_time || '';
              const badgeLabel = semester.startsWith('1') ? '1st Sem' : '2nd Sem';

              const sdFmt = startDate ? formatDateNice(startDate) : '';
              const edFmt = endDate ? formatDateNice(endDate) : '';
              const stFmt = startTime ? formatTimeNice(startTime) : '';
              const etFmt = endTime ? formatTimeNice(endTime) : '';

              let detailsHtml = '';
              if (sdFmt || edFmt || stFmt || etFmt) {
                detailsHtml = '<br><small class="text-muted">';
                if (sdFmt || edFmt) {
                  detailsHtml += 'Date: ' + escapeHtml(sdFmt) + (sdFmt && edFmt ? ' – ' : '') + escapeHtml(edFmt);
                }
                if (stFmt || etFmt) {
                  if (sdFmt || edFmt) detailsHtml += '<br>';
                  detailsHtml += 'Time: ' + escapeHtml(stFmt) + (stFmt && etFmt ? ' – ' : '') + escapeHtml(etFmt);
                }
                detailsHtml += '</small>';
              }

              li.innerHTML = `<div class="me-2">
                                <div class="d-flex align-items-center mb-1">
                                  <span class="badge bg-success me-2">${escapeHtml(badgeLabel)}</span>
                                  <strong>${escapeHtml(titleText)}</strong>
                                </div>
                                <small>${escapeHtml(descText)}</small>${detailsHtml}
                              </div>
                              <div>
                                <a href="<?= base_url('course') ?>/${escapeHtml(c.id || btn.getAttribute('data-course-id'))}/materials" class="btn btn-outline-success btn-sm">Materials</a>
                              </div>`;
              targetList.appendChild(li);
            }

            const grid = document.getElementById('coursesContainer');
            if (grid && grid.querySelectorAll('.col-md-6').length === 0) {
              const noAvail = document.getElementById('no-available');
              if (!noAvail) {
                const p = document.createElement('p');
                p.id = 'no-available';
                p.className = 'text-muted';
                p.textContent = 'No available courses right now.';
                grid.parentElement.appendChild(p);
              }
            }
          }
        })
        .catch(err => showFeedback('danger', 'Error enrolling: ' + err));
      });
    });
  }

  bindEnrollButtons();

  const searchForm = document.getElementById('searchForm');
  if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const term = document.getElementById('searchInput').value.trim();
      const url = new URL('<?= base_url('courses/search') ?>');
      if (term) url.searchParams.set('search_term', term);
      fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
          const grid = document.getElementById('coursesContainer');
          const body = searchForm.closest('.card-body');
          if (!grid) {
            const placeholder = document.getElementById('no-available');
            if (placeholder) placeholder.remove();
          }
          const courses = (data && data.courses) ? data.courses : [];

          // Build grid HTML
          let html = '';
          courses.forEach(c => {
            const startTime = c.start_time || '';
            const endTime = c.end_time || '';

            const stFmt = startTime ? formatTimeNice(startTime) : '';
            const etFmt = endTime ? formatTimeNice(endTime) : '';

            let detailsHtml = '';
            if (stFmt || etFmt) {
              detailsHtml = '<p class="mb-1 small text-muted">';
              detailsHtml += 'Time: ' + escapeHtml(stFmt) + (stFmt && etFmt ? ' – ' : '') + escapeHtml(etFmt);
              detailsHtml += '</p>';
            }

            html += `
            <div class="col-md-6">
              <div class="card h-100 course-card" data-card-course-id="${escapeHtml(c.id)}">
                <div class="card-body">
                  <h5 class="card-title">${escapeHtml(c.title)}</h5>
                  <p class="card-text">${escapeHtml(c.description)}</p>
                  ${detailsHtml}
                  <button class="btn btn-success enroll-btn" data-course-id="${escapeHtml(c.id)}">Enroll</button>
                </div>
              </div>
            </div>`;
          });

          let gridEl = document.getElementById('coursesContainer');
          if (!gridEl) {
            gridEl = document.createElement('div');
            gridEl.id = 'coursesContainer';
            gridEl.className = 'row g-3';
            body.appendChild(gridEl);
          }
          gridEl.innerHTML = html;

          // If no results, show placeholder text
          if (courses.length === 0) {
            const p = document.createElement('p');
            p.id = 'no-available';
            p.className = 'text-muted';
            p.textContent = 'No available courses right now.';
            if (gridEl.parentElement) gridEl.parentElement.appendChild(p);
          } else {
            const placeholder = document.getElementById('no-available');
            if (placeholder) placeholder.remove();
          }

          bindEnrollButtons();
        })
        .catch(err => showFeedback('danger', 'Search failed: ' + err));
    });
  }

  // Client-side instant filtering with jQuery
  if (window.jQuery) {
    const $ = window.jQuery;
    $(document).on('keyup', '#searchInput', function() {
      const value = ($(this).val() || '').toLowerCase();
      $('#coursesContainer .course-card').each(function() {
        const text = $(this).text().toLowerCase();
        const match = text.indexOf(value) > -1;
        $(this).closest('.col-md-6')[match ? 'show' : 'hide']();
      });
    });
  }

  function showFeedback(type, message) {
    const el = document.getElementById('feedback');
    el.className = 'alert alert-' + type;
    el.textContent = message;
    // ensure visible
    el.classList.remove('d-none');
  }

  // Read cookie helper for CSRF cookie method
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function formatDateNice(isoDate) {
    if (!isoDate) return '';
    const d = new Date(isoDate + 'T00:00:00');
    if (isNaN(d)) return isoDate;
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return d.toLocaleDateString(undefined, options);
  }

  function formatTimeNice(hms) {
    if (!hms) return '';
    const parts = String(hms).split(':');
    if (parts.length < 2) return hms;
    let h = parseInt(parts[0], 10);
    const m = parts[1];
    if (isNaN(h)) return hms;
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return h + ':' + m + ' ' + ampm;
  }
});
</script>
