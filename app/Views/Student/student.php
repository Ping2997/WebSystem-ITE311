<div class="container py-4">
  <h3 class="mb-3 text-muted text-green">Student Dashboard</h3>

  <p>Welcome, <?= esc($name) ?>!</p>

  <!-- Feedback message -->
  <div id="feedback" class="alert d-none" role="alert"></div>

  <div class="row g-3 mt-2">
    <!-- Courses (search only) -->
    <div class="col-lg-5">
      <div class="card shadow-sm mt-3 mt-lg-0">
        <div class="card-header fw-semibold bg-success text-white">Courses</div>
        <div class="card-body">
          <div class="mb-3">
            <form id="searchForm" class="d-flex" action="<?= base_url('courses/search') ?>" method="get">
              <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-primary" type="submit">
                  <i class="bi bi-search"></i> Search
                </button>
              </div>
            </form>
          </div>
          <p class="text-muted mb-1 small">Type a course name to search within your enrolled courses. Results will appear below.</p>
          <div id="coursesSearchResults" class="mt-3"></div>
        </div>
      </div>
    </div>

    <!-- My Courses (combined) -->
    <div class="col-lg-7">
      <div class="card shadow-sm mt-3 mt-lg-0">
        <div class="card-header fw-semibold bg-success text-white">My Courses</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
        <?php $hasCourses = !empty($enrolledFirstSem) || !empty($enrolledSecondSem); ?>

        <?php if ($hasCourses): ?>
          <?php foreach (array_merge($enrolledFirstSem ?? [], $enrolledSecondSem ?? []) as $course): ?>
            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start my-course-item">
              <div class="me-2">
                <div class="d-flex align-items-center mb-1">
                  <?php $sem = (string)($course['semester'] ?? '1st'); ?>
                  <span class="badge bg-success me-2"><?= $sem === '2nd' ? '2nd Sem' : '1st Sem' ?></span>
                  <strong><?= esc($course['title']); ?></strong>
                </div>
                <?php if (!empty($course['instructor_name'])): ?>
                  <small class="text-muted d-block mb-1">Instructor: <?= esc($course['instructor_name']); ?></small>
                <?php endif; ?>
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
          <li class="list-group-item text-muted">No courses assigned to you yet.</li>
        <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchForm = document.getElementById('searchForm');
  if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const term = (document.getElementById('searchInput').value || '').toLowerCase().trim();

      const items = document.querySelectorAll('.my-course-item');
      const resultsContainer = document.getElementById('coursesSearchResults');
      resultsContainer.innerHTML = '';

      if (!term) {
        // If search empty, don't show any result cards
        return;
      }

      // Heading
      const heading = document.createElement('h6');
      heading.className = 'fw-semibold mb-2';
      heading.textContent = `Search results for: "${term}"`;
      resultsContainer.appendChild(heading);

      let matchCount = 0;
      items.forEach(li => {
        const text = li.textContent.toLowerCase();
        if (text.indexOf(term) !== -1) {
          matchCount++;
          const clone = li.cloneNode(true);
          clone.classList.remove('my-course-item');
          const wrapper = document.createElement('div');
          wrapper.className = 'mb-2 p-2 border rounded bg-light';
          wrapper.appendChild(clone);
          resultsContainer.appendChild(wrapper);
        }
      });

      if (matchCount === 0) {
        const p = document.createElement('p');
        p.className = 'text-muted small';
        p.textContent = 'No courses matched your search.';
        resultsContainer.appendChild(p);
      }
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
