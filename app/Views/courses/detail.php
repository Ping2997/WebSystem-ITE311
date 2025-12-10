<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
  <?= $this->include('tempates/header') ?>

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0 text-green">Course Details</h3>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-green btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
      </a>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-1"><?= esc($course['title'] ?? 'Course') ?></h5>
            <p class="text-muted mb-2">Instructor: <?= esc($course['instructor_name'] ?? 'N/A') ?></p>
            <?php if (!empty($course['description'])): ?>
              <p class="mb-2"><?= esc($course['description']) ?></p>
            <?php endif; ?>
            <dl class="row mb-0 small">
              <dt class="col-sm-4">Semester</dt>
              <dd class="col-sm-8"><?= esc($course['semester'] ?? '—') ?></dd>
              <dt class="col-sm-4">Year Level</dt>
              <dd class="col-sm-8"><?= esc($course['year_level'] ?? '—') ?></dd>
              <dt class="col-sm-4">Capacity</dt>
              <dd class="col-sm-8">
                <?php
                  $cap = isset($course['capacity']) ? (int) $course['capacity'] : 0;
                  $enrolledCnt = isset($enrolledCount) ? (int) $enrolledCount : 0;
                  if ($cap > 0) {
                    echo esc($enrolledCnt) . ' / ' . esc($cap);
                  } else {
                    echo esc($enrolledCnt) . ' enrolled (no limit)';
                  }
                ?>
              </dd>
              <dt class="col-sm-4">Schedule</dt>
              <dd class="col-sm-8">
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
                <?php if ($sdFmt || $edFmt): ?>
                  <div>Date: <?= esc($sdFmt) ?><?= ($sdFmt && $edFmt) ? ' – ' : '' ?><?= esc($edFmt) ?></div>
                <?php endif; ?>
                <?php if ($stFmt || $etFmt): ?>
                  <div>Time: <?= esc($stFmt) ?><?= ($stFmt && $etFmt) ? ' – ' : '' ?><?= esc($etFmt) ?></div>
                <?php endif; ?>
                <?php if (!$sdFmt && !$edFmt && !$stFmt && !$etFmt): ?>
                  <div>—</div>
                <?php endif; ?>
              </dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <?php if (!empty($pendingEnrollments) && (session('role') === 'admin' || session('role') === 'teacher')): ?>
          <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Pending Approvals</div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table mb-0 table-sm align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Year</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; foreach ($pendingEnrollments as $s): ?>
                      <tr>
                        <td><?= $i++ ?></td>
                        <td class="pending-username"><?= esc($s['username']) ?></td>
                        <td class="pending-email"><?= esc($s['email']) ?></td>
                        <td class="pending-year"><?= esc($s['year_level'] ?? '—') ?></td>
                        <td>
                          <button class="btn btn-success btn-sm approve-enrollment-btn" data-enrollment-id="<?= esc($s['id']) ?>" data-username="<?= esc($s['username']) ?>" type="button">
                            <i class="fa fa-check me-1"></i> Approve
                          </button>
                          <button class="btn btn-danger btn-sm reject-enrollment-btn" data-enrollment-id="<?= esc($s['id']) ?>" data-username="<?= esc($s['username']) ?>" type="button">
                            <i class="fa fa-times me-1"></i> Reject
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <div id="noPendingApprovalsResults" class="text-center py-3" style="display: none;">
                  <i class="fas fa-search text-muted"></i>
                  <p class="text-muted mb-0 mt-2">No pending approvals found matching your search.</p>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="card shadow-sm h-100">
          <div class="card-header fw-semibold">Enrolled Students</div>
          <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
            <?php if (!empty($enrolledStudents)): ?>
              <!-- Search Filter -->
              <div class="mb-3">
                <form id="studentSearchForm" class="search-form">
                  <div class="input-group">
                    <span class="input-group-text bg-white">
                      <i class="fas fa-search text-secondary"></i>
                    </span>
                    <input type="text" id="studentSearchInput" name="search_term" class="form-control" placeholder="Search by username, email, or year level..." autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button" id="clearStudentSearchBtn" style="display: none;">
                      <i class="fas fa-times"></i> Clear
                    </button>
                  </div>
                  <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="studentSearchMode">Search active - Showing all students</span>
                  </small>
                </form>
              </div>
              
              <div class="table-responsive">
                <table class="table mb-0 table-sm align-middle" id="enrolledStudentsTable">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Year</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="enrolledStudentsTableBody">
                    <?php $i = 1; foreach ($enrolledStudents as $s): ?>
                      <tr class="student-row">
                        <td><?= $i++ ?></td>
                        <td class="student-username"><?= esc($s['username']) ?></td>
                        <td class="student-email"><?= esc($s['email']) ?></td>
                        <td class="student-year"><?= esc($s['year_level'] ?? '—') ?></td>
                        <td>
                          <?php
                            $status = $s['approval_status'] ?? 'pending';
                            $badgeClass = $status === 'approved' ? 'bg-success' : ($status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                          ?>
                          <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <div id="noStudentsFound" class="text-center py-3" style="display: none;">
                  <i class="fas fa-search text-muted"></i>
                  <p class="text-muted mb-0 mt-2">No students found matching your search.</p>
                </div>
              </div>
            <?php else: ?>
              <p class="text-muted px-3 py-2 mb-0">No students enrolled yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  jQuery(document).ready(function($) {
    // Student Search Filter - Client-side filtering
    var $searchInput = $('#studentSearchInput');
    var $clearBtn = $('#clearStudentSearchBtn');
    var $tableBody = $('#enrolledStudentsTableBody');
    var $noResults = $('#noStudentsFound');
    var $searchMode = $('#studentSearchMode');
    var totalStudents = $('.student-row').length;

    if ($searchInput.length > 0) {
      $searchInput.on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var $rows = $('.student-row');
        var visibleCount = 0;

        $rows.each(function() {
          var $row = $(this);
          var username = $row.find('.student-username').text().toLowerCase();
          var email = $row.find('.student-email').text().toLowerCase();
          var year = $row.find('.student-year').text().toLowerCase();

          if (username.includes(searchTerm) || email.includes(searchTerm) || year.includes(searchTerm)) {
            $row.show();
            visibleCount++;
          } else {
            $row.hide();
          }
        });

        // Update row numbers
        $rows.filter(':visible').each(function(index) {
          $(this).find('td:first').text(index + 1);
        });

        // Show/hide clear button
        if (searchTerm.length > 0) {
          $clearBtn.show();
          $searchMode.text('Search active - Showing ' + visibleCount + ' of ' + totalStudents + ' student(s)');
        } else {
          $clearBtn.hide();
          $searchMode.text('Search active - Showing all students');
        }

        // Show/hide no results message
        if (visibleCount === 0 && searchTerm.length > 0) {
          $noResults.show();
          $tableBody.hide();
        } else {
          $noResults.hide();
          $tableBody.show();
        }
      });

      // Clear search button
      $clearBtn.on('click', function() {
        $searchInput.val('');
        $('.student-row').show();
        $('.student-row').each(function(index) {
          $(this).find('td:first').text(index + 1);
        });
        $(this).hide();
        $searchMode.text('Search active - Showing all students');
        $noResults.hide();
        $tableBody.show();
      });
    }

    // Approve enrollment
    $('.approve-enrollment-btn').on('click', function() {
      var enrollmentId = $(this).data('enrollment-id');
      var username = $(this).data('username');
      var $btn = $(this);
      
      if (!confirm('Approve enrollment for "' + username + '"?')) {
        return;
      }
      
      $btn.prop('disabled', true);
      $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Approving...');
      
      $.ajax({
        url: '<?= base_url('course/approve-enrollment/') ?>' + enrollmentId,
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        success: function(data) {
          if (data.success) {
            alert(data.message || 'Enrollment approved successfully!');
            location.reload();
          } else {
            alert('Error: ' + (data.message || 'Failed to approve enrollment'));
            $btn.prop('disabled', false);
            $btn.html('<i class="fa fa-check me-1"></i> Approve');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
          $btn.prop('disabled', false);
          $btn.html('<i class="fa fa-check me-1"></i> Approve');
        }
      });
    });
    
    // Reject enrollment
    $('.reject-enrollment-btn').on('click', function() {
      var enrollmentId = $(this).data('enrollment-id');
      var username = $(this).data('username');
      var $btn = $(this);
      
      if (!confirm('Reject enrollment for "' + username + '"?')) {
        return;
      }
      
      $btn.prop('disabled', true);
      $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Rejecting...');
      
      $.ajax({
        url: '<?= base_url('course/reject-enrollment/') ?>' + enrollmentId,
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        success: function(data) {
          if (data.success) {
            alert(data.message || 'Enrollment rejected.');
            location.reload();
          } else {
            alert('Error: ' + (data.message || 'Failed to reject enrollment'));
            $btn.prop('disabled', false);
            $btn.html('<i class="fa fa-times me-1"></i> Reject');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
          $btn.prop('disabled', false);
          $btn.html('<i class="fa fa-times me-1"></i> Reject');
        }
      });
    });
  });
  
  // Pending Approvals Search Filter
  (function() {
    function initPendingApprovalsSearch() {
      var searchInput = document.getElementById('pendingApprovalsSearchInput');
      var clearBtn = document.getElementById('clearPendingApprovalsSearchBtn');
      var tableBody = document.getElementById('pendingApprovalsTableBody');
      
      if (!searchInput || !tableBody) {
        setTimeout(initPendingApprovalsSearch, 50);
        return;
      }
      
      function filterPendingApprovals() {
        var searchTerm = searchInput.value.toLowerCase().trim();
        var rows = tableBody.querySelectorAll('.pending-approval-row');
        var visibleCount = 0;
        var i;
        var noResultsMsg = document.getElementById('noPendingApprovalsResults');
        
        for (i = 0; i < rows.length; i++) {
          var row = rows[i];
          var usernameEl = row.querySelector('.pending-username');
          var emailEl = row.querySelector('.pending-email');
          var yearEl = row.querySelector('.pending-year');
          
          var usernameText = usernameEl ? usernameEl.textContent.toLowerCase() : '';
          var emailText = emailEl ? emailEl.textContent.toLowerCase() : '';
          var yearText = yearEl ? yearEl.textContent.toLowerCase() : '';
          
          var matches = searchTerm === '' || 
                       usernameText.indexOf(searchTerm) !== -1 ||
                       emailText.indexOf(searchTerm) !== -1 ||
                       yearText.indexOf(searchTerm) !== -1;
          
          if (matches) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        }
        
        // Update row numbers
        var visibleRows = tableBody.querySelectorAll('.pending-approval-row[style=""]');
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
      
      searchInput.addEventListener('input', filterPendingApprovals);
      searchInput.addEventListener('keyup', filterPendingApprovals);
      
      if (clearBtn) {
        clearBtn.addEventListener('click', function() {
          searchInput.value = '';
          filterPendingApprovals();
        });
      }
    }
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initPendingApprovalsSearch);
    } else {
      initPendingApprovalsSearch();
    }
  })();
  </script>
</body>
</html>
