<div class="container py-4">
  <h3 class="mb-3 text-muted text-green">Student Dashboard</h3>

  <p>Welcome, <?= esc($name) ?>!</p>

  <!-- Feedback message -->
  <div id="feedback" class="alert d-none" role="alert"></div>

  <!-- My Courses -->
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold bg-success text-white">My Courses</div>
        <div class="card-body">
          <ul id="my-courses-list" class="list-group list-group-flush">
            <?php if (!empty($enrolledCourses)): ?>
              <?php foreach ($enrolledCourses as $course): ?>
                <li class="list-group-item">
                  <strong><?= esc($course['title']); ?></strong><br>
                  <small><?= esc($course['description']); ?></small>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-muted" id="no-enrolled">No enrolled courses yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Upcoming Deadlines</div>
        <div class="card-body">
          <p class="text-muted mb-0">No deadlines.</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Available Courses -->
  <div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold bg-success text-white">Available Courses</div>
    <div class="card-body">
      <?php if (!empty($availableCourses)): ?>
        <div id="available-courses-grid" class="row g-3">
          <?php foreach ($availableCourses as $course): ?>
            <div class="col-md-6">
              <div class="card h-100" data-card-course-id="<?= esc($course['id']); ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= esc($course['title']); ?></h5>
                  <p class="card-text"><?= esc($course['description']); ?></p>
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
  document.querySelectorAll('.enroll-btn').forEach(button => {
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
          // Disable button and update text
          btn.disabled = true;
          btn.textContent = 'Enrolled';

          // Move card from Available Courses to My Courses list
          const card = btn.closest('[data-card-course-id]');
          if (card) {
            const gridCol = card.closest('.col-md-6');
            if (gridCol) gridCol.remove();
          }

          // Remove placeholder if present
          const placeholder = document.getElementById('no-enrolled');
          if (placeholder) placeholder.remove();

          // Append to My Courses list
          const list = document.getElementById('my-courses-list');
          const c = data.course || {};
          const li = document.createElement('li');
          li.className = 'list-group-item';
          li.innerHTML = `<strong>${escapeHtml(c.title || btn.closest('.card-body').querySelector('.card-title').textContent)}</strong><br>
                          <small>${escapeHtml(c.description || btn.closest('.card-body').querySelector('.card-text').textContent)}</small>`;
          list.appendChild(li);

          // If no more available cards, show placeholder
          const grid = document.getElementById('available-courses-grid');
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
});
</script>
