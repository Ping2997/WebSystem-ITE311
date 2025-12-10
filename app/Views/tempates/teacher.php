<div class="dashboard-container">
  <div class="dashboard-header">
    <h1><i class="fas fa-chalkboard-teacher me-2"></i>Teacher Dashboard</h1>
    <p class="text-secondary">Welcome back, <strong><?= esc($name) ?></strong>! Manage your courses and students.</p>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-book me-2"></i>My Courses
    </div>
    <div class="card-body" style="background: transparent; padding: 2rem 2rem 1rem 2rem;">
      <!-- Search Filter -->
      <?php if (!empty($courses)): ?>
        <div class="mb-4">
          <form id="teacherCourseSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="teacherCourseSearchInput" name="search_term" class="form-control" placeholder="Search courses by title..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="teacherClearSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($courses)): ?>
        <div id="teacherCoursesList" class="list-group list-group-flush">
          <?php foreach ($courses as $c): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center course-item">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <div class="stat-icon" style="width: 2.5rem; height: 2.5rem; font-size: 1.25rem;">
                    <i class="fas fa-book"></i>
                  </div>
                </div>
                <div>
                  <div class="fw-bold mb-1"><?= esc($c['title']) ?></div>
                  <small class="text-muted">Click to manage course details and materials</small>
                </div>
              </div>
              <div class="btn-group" role="group">
                <a href="<?= base_url('admin/course/' . (int)$c['id']) ?>" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-info-circle me-1"></i> Details
                </a>
                <a href="<?= base_url('admin/course/' . (int)$c['id'] . '/upload') ?>" class="btn btn-outline-success btn-sm">
                  <i class="fas fa-upload me-1"></i> Upload
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-book-open"></i>
          <p class="mb-0">No courses assigned yet.</p>
          <small class="text-muted">Contact your administrator to get assigned to courses.</small>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-clock me-2"></i>Pending Enrollment Requests
    </div>
    <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
      <?php if (!empty($pendingEnrollments)): ?>
        <!-- Search Filter for Pending Enrollments -->
        <div class="mb-3">
          <form id="enrollmentSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="enrollmentSearchInput" name="search_term" class="form-control" placeholder="Search by student name, email, course, or year level..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="clearEnrollmentSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="enrollmentTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Request Date</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="enrollmentTableBody">
              <?php $i = 1; foreach ($pendingEnrollments as $enrollment): ?>
                <tr class="enrollment-row">
                  <td><?= $i++ ?></td>
                  <td class="enrollment-student">
                    <div>
                      <strong><?= esc($enrollment['username']) ?></strong>
                      <br><small class="text-muted"><?= esc($enrollment['email']) ?></small>
                    </div>
                  </td>
                  <td class="enrollment-course">
                    <a href="<?= base_url('admin/course/' . (int)$enrollment['course_id']) ?>" class="text-decoration-none">
                      <i class="fas fa-book me-1"></i><?= esc($enrollment['course_title']) ?>
                    </a>
                  </td>
                  <td class="enrollment-year">
                    <span class="badge bg-info text-dark"><?= esc($enrollment['year_level'] ?? '—') ?></span>
                  </td>
                  <td>
                    <small><?= $enrollment['enrollment_date'] ? date('M j, Y g:i A', strtotime($enrollment['enrollment_date'])) : '—' ?></small>
                  </td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="<?= base_url('admin/course/' . (int)$enrollment['course_id']) ?>" class="btn btn-outline-primary" title="View Course Details">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div id="noEnrollmentResults" class="text-center py-3" style="display: none;">
            <i class="fas fa-search text-muted"></i>
            <p class="text-muted mb-0 mt-2">No enrollment requests found matching your search.</p>
          </div>
        </div>
        <div class="mt-3 text-center">
          <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Click on course name or view button to approve/reject enrollments from the course details page.
          </small>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-check-circle"></i>
          <p class="mb-0">No pending enrollment requests.</p>
          <small class="text-muted">All enrollment requests for your courses have been processed.</small>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <i class="fas fa-file-alt me-2"></i>New Submissions
    </div>
    <div class="card-body">
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p class="mb-0">No new submissions.</p>
        <small class="text-muted">Student submissions will appear here when available.</small>
      </div>
    </div>
  </div>
</div>

<script>
// Teacher Course Search Filter - Pure JavaScript
(function() {
  function initTeacherCourseSearch() {
    var searchInput = document.getElementById('teacherCourseSearchInput');
    var clearBtn = document.getElementById('teacherClearSearchBtn');
    var coursesList = document.getElementById('teacherCoursesList');
    
    if (!searchInput || !coursesList) {
      setTimeout(initTeacherCourseSearch, 50);
      return;
    }
    
    function filterCourses() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var courseItems = coursesList.querySelectorAll('.course-item');
      var visibleCount = 0;
      var i;
      var noResultsMsg = coursesList.querySelector('.no-results-message-teacher');
      
      for (i = 0; i < courseItems.length; i++) {
        var item = courseItems[i];
        var titleEl = item.querySelector('.fw-bold');
        var title = titleEl ? titleEl.textContent.toLowerCase() : '';
        
        var matches = searchTerm === '' || title.indexOf(searchTerm) !== -1;
        
        if (matches) {
          item.style.setProperty('display', 'flex', 'important');
          item.classList.remove('d-none');
          visibleCount++;
        } else {
          item.style.setProperty('display', 'none', 'important');
          item.classList.add('d-none');
        }
      }
      
      if (clearBtn) {
        clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
      }
      
      if (visibleCount === 0 && searchTerm.length > 0) {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('div');
          noResultsMsg.className = 'empty-state no-results-message-teacher';
          noResultsMsg.style.padding = '2rem';
          noResultsMsg.style.textAlign = 'center';
          noResultsMsg.innerHTML = '<i class="fas fa-search text-muted"></i><p class="mb-0 text-muted">No courses found matching your search.</p>';
          coursesList.appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = 'block';
      } else {
        if (noResultsMsg) {
          noResultsMsg.style.display = 'none';
        }
      }
    }
    
    searchInput.addEventListener('input', filterCourses);
    searchInput.addEventListener('keyup', filterCourses);
    searchInput.addEventListener('paste', function() {
      setTimeout(filterCourses, 10);
    });
    
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        var courseItems = coursesList.querySelectorAll('.course-item');
        for (var j = 0; j < courseItems.length; j++) {
          courseItems[j].style.setProperty('display', 'flex', 'important');
          courseItems[j].classList.remove('d-none');
        }
        filterCourses();
      });
    }
    
    // Initial call to set up state
    filterCourses();
  }
  
  // Enrollment Search Filter - Pure JavaScript
  function initEnrollmentSearch() {
    var searchInput = document.getElementById('enrollmentSearchInput');
    var clearBtn = document.getElementById('clearEnrollmentSearchBtn');
    var tableBody = document.getElementById('enrollmentTableBody');
    
    if (!searchInput || !tableBody) {
      setTimeout(initEnrollmentSearch, 50);
      return;
    }
    
    function filterEnrollments() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var rows = tableBody.querySelectorAll('.enrollment-row');
      var visibleCount = 0;
      var i;
      var noResultsMsg = document.getElementById('noEnrollmentResults');
      
      for (i = 0; i < rows.length; i++) {
        var row = rows[i];
        var studentEl = row.querySelector('.enrollment-student');
        var courseEl = row.querySelector('.enrollment-course');
        var yearEl = row.querySelector('.enrollment-year');
        
        var studentText = studentEl ? studentEl.textContent.toLowerCase() : '';
        var courseText = courseEl ? courseEl.textContent.toLowerCase() : '';
        var yearText = yearEl ? yearEl.textContent.toLowerCase() : '';
        
        var matches = searchTerm === '' || 
                     studentText.indexOf(searchTerm) !== -1 ||
                     courseText.indexOf(searchTerm) !== -1 ||
                     yearText.indexOf(searchTerm) !== -1;
        
        if (matches) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      }
      
      // Update row numbers
      var visibleRows = tableBody.querySelectorAll('.enrollment-row[style=""]');
      for (i = 0; i < visibleRows.length; i++) {
        visibleRows[i].querySelector('td:first-child').textContent = i + 1;
      }
      
      if (clearBtn) {
        clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
      }
      
      if (noResultsMsg) {
        if (visibleCount === 0 && searchTerm.length > 0) {
          noResultsMsg.style.display = 'block';
          tableBody.style.display = 'none';
        } else {
          noResultsMsg.style.display = 'none';
          tableBody.style.display = '';
        }
      }
    }
    
    searchInput.addEventListener('input', filterEnrollments);
    searchInput.addEventListener('keyup', filterEnrollments);
    
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterEnrollments();
      });
    }
  }
  
  // Initialize both searches
  function initAll() {
    initTeacherCourseSearch();
    initEnrollmentSearch();
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
</script>

