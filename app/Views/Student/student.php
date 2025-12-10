<div class="dashboard-container">
  <div class="dashboard-header mb-4">
    <h1 class="mb-2"><i class="fas fa-graduation-cap me-2"></i>Student Dashboard</h1>
    <p class="text-secondary mb-0">Welcome back, <strong><?= esc($name) ?></strong>! Continue your learning journey.</p>
  </div>

  <!-- Feedback message -->
  <div id="feedback" class="alert d-none mb-4" role="alert"></div>

  <div class="row g-4">
    <!-- Available Courses -->
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-book-open me-2"></i>Available Courses
        </div>
        <div class="card-body" style="background: transparent; padding: 2rem 2rem 1rem 2rem;">
          <!-- Search Filter -->
          <div class="mb-4">
            <form id="courseSearchForm" class="search-form">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-search text-secondary"></i>
                </span>
                <input type="text" id="courseSearchInput" name="search_term" class="form-control" placeholder="Search courses by title or description..." autocomplete="off">
                <button class="btn btn-success" type="submit" id="serverSearchBtn">
                  <i class="fas fa-search me-1"></i> Search
                </button>
                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" style="display: none;">
                  <i class="fas fa-times"></i> Clear
                </button>
              </div>
              <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>
                <span id="searchMode">Client-side filtering active</span>
              </small>
            </form>
          </div>
          
          <?php if (!empty($availableCourses)): ?>
            <div id="availableCoursesList">
              <?php foreach ($availableCourses as $course): ?>
                <div class="course-card shadow-sm" data-course-id="<?= esc($course['id']) ?>">
                  <!-- Course Title and Badge -->
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 fw-bold fs-5"><?= esc($course['title']) ?></h6>
                    <span class="badge bg-primary">New</span>
                  </div>
                  
                  <!-- Course Description -->
                  <?php if (!empty($course['description'])): ?>
                    <p class="card-text text-secondary mb-4"><?= esc($course['description']) ?></p>
                  <?php endif; ?>
                  
                  <!-- Course Details -->
                  <div class="course-details mb-4">
                    <?php if (!empty($course['instructor_name'])): ?>
                      <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-tie me-2 text-primary"></i>
                        <span class="text-secondary"><strong>Instructor:</strong> <?= esc($course['instructor_name']) ?></span>
                      </div>
                    <?php endif; ?>
                    
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
                    
                    <div class="row g-3 mb-3">
                      <?php if ($sdFmt || $edFmt): ?>
                        <div class="col-12 col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            <span class="text-secondary"><?= esc($sdFmt) ?><?= ($sdFmt && $edFmt) ? ' – ' . esc($edFmt) : '' ?></span>
                          </div>
                        </div>
                      <?php endif; ?>
                      
                      <?php if ($stFmt || $etFmt): ?>
                        <div class="col-12 col-md-6">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <span class="text-secondary"><?= esc($stFmt) ?><?= ($stFmt && $etFmt) ? ' – ' . esc($etFmt) : '' ?></span>
                          </div>
                        </div>
                      <?php endif; ?>
                    </div>
                    
                    <?php
                      $capacity = isset($course['capacity']) ? (int) $course['capacity'] : 0;
                      $enrolledCount = isset($course['enrolled_count']) ? (int) $course['enrolled_count'] : 0;
                    ?>
                    
                    <?php if ($capacity > 0): ?>
                      <div class="capacity-info">
                        <small class="text-secondary d-block mb-2">Capacity: <?= esc($enrolledCount) ?> / <?= esc($capacity) ?> enrolled</small>
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $capacity > 0 ? ($enrolledCount / $capacity * 100) : 0 ?>%"></div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Enroll Button -->
                  <button class="btn btn-success w-100 enroll-btn mt-auto" data-course-id="<?= esc($course['id']) ?>" data-course-title="<?= esc($course['title']) ?>" style="padding: 0.75rem;">
                    <i class="fas fa-plus me-2"></i> Enroll Now
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-book"></i>
              <p class="mb-0">No available courses matching your year level.</p>
              <small class="text-muted">Check back later for new courses.</small>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- My Courses (combined) -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-list me-2"></i>My Courses
        </div>
        <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
          <?php $hasCourses = !empty($enrolledFirstSem) || !empty($enrolledSecondSem); ?>
          
          <?php if ($hasCourses): ?>
            <!-- Search Filter -->
            <div class="mb-3">
              <form id="myCoursesSearchForm" class="search-form" onsubmit="return false;">
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-search text-secondary"></i>
                  </span>
                  <input type="text" id="myCoursesSearchInput" name="search_term" class="form-control" placeholder="Search my courses by title, instructor, or description..." autocomplete="off">
                  <button class="btn btn-outline-secondary" type="button" id="clearMyCoursesSearchBtn" style="display: none;">
                    <i class="fas fa-times"></i> Clear
                  </button>
                </div>
              </form>
            </div>
          <?php endif; ?>
          
          <ul class="list-group list-group-flush" id="myCoursesList">
        <?php if ($hasCourses): ?>
                    <?php foreach (array_merge($enrolledFirstSem ?? [], $enrolledSecondSem ?? []) as $course): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start my-course-item" data-course-title="<?= esc(strtolower($course['title'])) ?>" data-instructor="<?= esc(strtolower($course['instructor_name'] ?? '')) ?>" data-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
              <div class="flex-grow-1 me-3">
                <div class="d-flex align-items-center mb-2">
                  <?php $sem = (string)($course['semester'] ?? '1st'); ?>
                  <span class="badge bg-success me-2"><?= $sem === '2nd' ? '2nd Sem' : '1st Sem' ?></span>
                  <h6 class="mb-0 fw-bold"><?= esc($course['title']); ?></h6>
                </div>
                <?php if (!empty($course['instructor_name'])): ?>
                  <p class="small mb-2 text-secondary">
                    <i class="fas fa-user-tie me-1 text-primary"></i>
                    <strong>Instructor:</strong> <?= esc($course['instructor_name']); ?>
                  </p>
                <?php endif; ?>
                <?php if (!empty($course['description'])): ?>
                  <p class="small text-secondary mb-2"><?= esc($course['description']); ?></p>
                <?php endif; ?>
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
                  <div class="small text-secondary">
                    <?php if ($sdFmt || $edFmt): ?>
                      <i class="fas fa-calendar-alt me-1"></i><?= esc($sdFmt) ?><?= ($sdFmt && $edFmt) ? ' – ' . esc($edFmt) : '' ?>
                    <?php endif; ?>
                    <?php if ($stFmt || $etFmt): ?>
                      <br><i class="fas fa-clock me-1"></i><?= esc($stFmt) ?><?= ($stFmt && $etFmt) ? ' – ' . esc($etFmt) : '' ?>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="flex-shrink-0">
                <a href="<?= base_url('course/' . $course['id'] . '/materials') ?>" class="btn btn-outline-success btn-sm">
                  <i class="fas fa-file-alt me-1"></i> Materials
                </a>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <p class="mb-0">No courses assigned to you yet.</p>
            <small class="text-muted">Enroll in courses from the available courses section.</small>
          </div>
        <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
jQuery(document).ready(function($) {
  // Ensure jQuery is available
  if (typeof $ === 'undefined') {
    console.error('jQuery is not loaded');
    return;
  }
      // Store original courses HTML for reset
      var originalHTML = $('#availableCoursesList').html();
      var isServerSearch = false;
      
      // Client-side filtering with jQuery (Step 5: Instant feedback)
      $('#courseSearchInput').off('input').on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var $courseCards = $('.course-card');
        var visibleCount = 0;
        
        // Only do client-side filtering if not in server search mode
        if (!isServerSearch) {
          $courseCards.each(function() {
            var $card = $(this);
            var title = $card.find('.card-title').text().toLowerCase();
            var description = $card.find('.card-text').text().toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
              $card.show();
              visibleCount++;
            } else {
              $card.hide();
            }
          });
          
          // Show/hide clear button
          if (searchTerm.length > 0) {
            $('#clearSearchBtn').show();
            $('#searchMode').text('Client-side filtering: Showing ' + visibleCount + ' result(s)');
          } else {
            $('#clearSearchBtn').hide();
            $('#searchMode').text('Client-side filtering active');
          }
          
          // Show message if no results
          var $noResultsMsg = $('.no-results-message');
          if (visibleCount === 0 && searchTerm.length > 0) {
            if ($noResultsMsg.length === 0) {
              $noResultsMsg = $('<div class="empty-state no-results-message"><i class="fas fa-search"></i><p class="mb-0">No courses found matching your search.</p></div>');
              $('#availableCoursesList').append($noResultsMsg);
            }
            $noResultsMsg.show();
          } else {
            $noResultsMsg.hide();
          }
        }
      });
  
      // Server-side search with AJAX (Step 6: Comprehensive search)
      $('#courseSearchForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#courseSearchInput').val().trim();
        
        if (searchTerm.length === 0) {
          // Reset to original if empty
          resetToOriginal();
          return;
        }
        
        isServerSearch = true;
        $('#searchMode').html('<i class="fas fa-spinner fa-spin me-1"></i>Searching database...');
        
        // Show loading state
        $('#availableCoursesList').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x me-2"></i><p class="mt-3">Searching courses in database...</p></div>');
        
        // AJAX request to server
        $.ajax({
          url: '<?= base_url('courses/search') ?>',
          method: 'GET',
          data: { search_term: searchTerm },
          dataType: 'json',
          success: function(response) {
            if (response.courses && response.courses.length > 0) {
              renderCoursesFromServer(response.courses);
              $('#searchMode').text('Server-side search: Found ' + response.courses.length + ' result(s)');
            } else {
              $('#availableCoursesList').html('<div class="empty-state"><i class="fas fa-search"></i><p class="mb-0">No courses found matching "' + escapeHtml(searchTerm) + '" in database.</p></div>');
              $('#searchMode').text('Server-side search: No results found');
            }
            $('#clearSearchBtn').show();
            attachEnrollListeners();
          },
          error: function(xhr, status, error) {
            console.error('Search error:', error);
            $('#availableCoursesList').html('<div class="alert alert-danger">An error occurred while searching. Please try again.</div>');
            $('#searchMode').text('Search error occurred');
          }
        });
      });
      
      // Clear search button
      $('#clearSearchBtn').off('click').on('click', function() {
        resetToOriginal();
      });
      
      // Function to reset to original courses
      function resetToOriginal() {
        $('#courseSearchInput').val('');
        $('#availableCoursesList').html(originalHTML);
        $('#clearSearchBtn').hide();
        $('#searchMode').text('Client-side filtering active');
        isServerSearch = false;
        attachEnrollListeners();
      }
  
      // Helper functions (defined first)
      function formatDate(dateStr) {
        var d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
      }
      
      function formatTime(hms) {
        if (!hms) return '';
        var parts = String(hms).split(':');
        if (parts.length < 2) return hms;
        var h = parseInt(parts[0], 10);
        var m = parts[1];
        if (isNaN(h)) return hms;
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        if (h === 0) h = 12;
        return h + ':' + m + ' ' + ampm;
      }
      
      function escapeHtml(text) {
        var div = $('<div>');
        return div.text(text).html();
      }
      
      // Function to attach enroll button listeners
      function attachEnrollListeners() {
        // Enroll button functionality using jQuery
        $('.enroll-btn').off('click').on('click', function() {
          var $btn = $(this);
          var courseId = $btn.data('course-id');
          var courseTitle = $btn.data('course-title');
          
          if (!confirm('Enroll in "' + courseTitle + '"?')) {
            return;
          }
          
          // Disable button during request
          $btn.prop('disabled', true);
          $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Enrolling...');
          
          $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ course_id: courseId }),
            success: function(data) {
              if (data.success) {
                showFeedback('success', data.message || 'Enrollment request submitted successfully! Waiting for teacher approval.');
                // Remove the course card from available courses
                var $courseCard = $btn.closest('.course-card');
                $courseCard.fadeOut(300, function() {
                  $(this).remove();
                  // If no more courses, show message
                  if ($('#availableCoursesList').children().length === 0) {
                    $('#availableCoursesList').html('<p class="text-muted mb-0">No available courses matching your year level.</p>');
                  }
                });
                // Reload page after 2 seconds to show updated enrolled courses
                setTimeout(function() {
                  location.reload();
                }, 2000);
              } else {
                showFeedback('danger', data.message || 'Failed to enroll. Please try again.');
                $btn.prop('disabled', false);
                $btn.html('<i class="fa fa-plus me-1"></i> Enroll');
              }
            },
            error: function(xhr, status, error) {
              console.error('Error:', error);
              showFeedback('danger', 'An error occurred while enrolling. Please try again.');
              $btn.prop('disabled', false);
              $btn.html('<i class="fa fa-plus me-1"></i> Enroll');
            }
          });
        });
      }
      
      // Feedback function
      function showFeedback(type, message) {
        var $el = $('#feedback');
        $el.removeClass('d-none alert-success alert-danger alert-warning alert-info')
           .addClass('alert-' + type)
           .text(message)
           .show();
        
        // Auto hide after 5 seconds
        setTimeout(function() {
          $el.fadeOut(function() {
            $(this).addClass('d-none');
          });
        }, 5000);
      }
      
      // Function to render courses from server response
      function renderCoursesFromServer(courses) {
        var html = '';
        
        courses.forEach(function(course) {
          var sd = course.start_date || '';
          var ed = course.end_date || '';
          var st = course.start_time || '';
          var et = course.end_time || '';
          var sdFmt = sd ? formatDate(sd) : '';
          var edFmt = ed ? formatDate(ed) : '';
          var stFmt = st ? formatTime(st) : '';
          var etFmt = et ? formatTime(et) : '';
          var capacity = parseInt(course.capacity) || 0;
          var enrolledCount = parseInt(course.enrolled_count) || 0;
        
          html += '<div class="course-card shadow-sm" data-course-id="' + course.id + '">';
          html += '<div class="d-flex justify-content-between align-items-center mb-3">';
          html += '<h6 class="card-title mb-0 fw-bold fs-5">' + escapeHtml(course.title) + '</h6>';
          html += '<span class="badge bg-primary">New</span>';
          html += '</div>';
          
          if (course.description) {
            html += '<p class="card-text text-secondary mb-4">' + escapeHtml(course.description) + '</p>';
          }
          
          html += '<div class="course-details mb-4">';
          
          if (course.instructor_name) {
            html += '<div class="d-flex align-items-center mb-3">';
            html += '<i class="fas fa-user-tie me-2 text-primary"></i>';
            html += '<span class="text-secondary"><strong>Instructor:</strong> ' + escapeHtml(course.instructor_name) + '</span>';
            html += '</div>';
          }
          
          html += '<div class="row g-3 mb-3">';
          
          if (sdFmt || edFmt) {
            html += '<div class="col-12 col-md-6"><div class="d-flex align-items-center">';
            html += '<i class="fas fa-calendar-alt me-2 text-primary"></i>';
            html += '<span class="text-secondary">' + sdFmt + (sdFmt && edFmt ? ' – ' + edFmt : '') + '</span>';
            html += '</div></div>';
          }
          
          if (stFmt || etFmt) {
            html += '<div class="col-12 col-md-6"><div class="d-flex align-items-center">';
            html += '<i class="fas fa-clock me-2 text-primary"></i>';
            html += '<span class="text-secondary">' + stFmt + (stFmt && etFmt ? ' – ' + etFmt : '') + '</span>';
            html += '</div></div>';
          }
          
          html += '</div>';
          
          if (capacity > 0) {
            html += '<div class="capacity-info">';
            html += '<small class="text-secondary d-block mb-2">Capacity: ' + enrolledCount + ' / ' + capacity + ' enrolled</small>';
            html += '<div class="progress" style="height: 8px; border-radius: 4px;">';
            html += '<div class="progress-bar bg-success" role="progressbar" style="width: ' + (capacity > 0 ? (enrolledCount / capacity * 100) : 0) + '%"></div>';
            html += '</div></div>';
          }
          
          html += '</div>';
          html += '<button class="btn btn-success w-100 enroll-btn mt-auto" data-course-id="' + course.id + '" data-course-title="' + escapeHtml(course.title) + '" style="padding: 0.75rem;">';
          html += '<i class="fas fa-plus me-2"></i> Enroll Now';
          html += '</button>';
          html += '</div>';
        });
        
        $('#availableCoursesList').html(html);
      }
      
          // Initial attachment of enroll listeners on page load
          attachEnrollListeners();
        });
        
        // My Courses Search Filter
        (function() {
          function initMyCoursesSearch() {
            var searchInput = document.getElementById('myCoursesSearchInput');
            var clearBtn = document.getElementById('clearMyCoursesSearchBtn');
            var coursesList = document.getElementById('myCoursesList');
            
            if (!searchInput || !coursesList) {
              setTimeout(initMyCoursesSearch, 50);
              return;
            }
            
            function filterMyCourses() {
              var searchTerm = searchInput.value.toLowerCase().trim();
              var courseItems = coursesList.querySelectorAll('.my-course-item');
              var visibleCount = 0;
              var i;
              var noResultsMsg = coursesList.querySelector('.no-my-courses-results');
              
              for (i = 0; i < courseItems.length; i++) {
                var item = courseItems[i];
                var title = item.getAttribute('data-course-title') || '';
                var instructor = item.getAttribute('data-instructor') || '';
                var description = item.getAttribute('data-description') || '';
                
                var matches = searchTerm === '' || 
                             title.indexOf(searchTerm) !== -1 ||
                             instructor.indexOf(searchTerm) !== -1 ||
                             description.indexOf(searchTerm) !== -1;
                
                if (matches) {
                  item.style.setProperty('display', 'flex', 'important');
                  visibleCount++;
                } else {
                  item.style.setProperty('display', 'none', 'important');
                }
              }
              
              if (clearBtn) {
                clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
              }
              
              if (visibleCount === 0 && searchTerm.length > 0) {
                if (!noResultsMsg) {
                  noResultsMsg = document.createElement('div');
                  noResultsMsg.className = 'empty-state no-my-courses-results';
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
            
            searchInput.addEventListener('input', filterMyCourses);
            searchInput.addEventListener('keyup', filterMyCourses);
            
            if (clearBtn) {
              clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterMyCourses();
              });
            }
          }
          
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMyCoursesSearch);
          } else {
            initMyCoursesSearch();
          }
        })();
        </script>
