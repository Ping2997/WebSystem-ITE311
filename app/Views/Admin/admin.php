<div class="dashboard-container">
  <div class="dashboard-header mb-4">
    <h1 class="mb-2"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
    <p class="text-secondary mb-0">Welcome back, <strong><?= esc($name) ?></strong>! Manage your learning management system.</p>
  </div>

  <!-- Quick Actions -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      <i class="fas fa-bolt me-2"></i>Quick Actions
    </div>
    <div class="card-body" style="background: transparent; padding: 2rem;">
      <div class="d-flex flex-wrap gap-3">
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#subjectModal" style="padding: 0.75rem 1.5rem;">
          <i class="fas fa-plus me-2"></i> Add Course
        </button>
        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#studentModal" style="padding: 0.75rem 1.5rem;">
          <i class="fas fa-user-plus me-2"></i> Add Student
        </button>
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#teacherModal" style="padding: 0.75rem 1.5rem;">
          <i class="fas fa-chalkboard-teacher me-2"></i> Add Teacher
        </button>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
      <div class="stat-card shadow-sm" style="cursor: pointer;" onclick="toggleSection('usersSection')" title="Click to view all users">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?= isset($totalUsers) ? (int) $totalUsers : '0' ?></div>
        <div class="stat-label">Total Users</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="stat-card shadow-sm" style="border-left-color: var(--secondary); cursor: pointer;" onclick="toggleSection('coursesSection')" title="Click to view all courses">
        <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);">
          <i class="fas fa-book"></i>
        </div>
        <div class="stat-value"><?= isset($totalCourses) ? (int) $totalCourses : '0' ?></div>
        <div class="stat-label">Total Courses</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="stat-card shadow-sm" style="border-left-color: var(--accent); cursor: pointer;" onclick="toggleSection('activeStudentsSection')" title="Click to view active students by year">
        <div class="stat-icon" style="background: linear-gradient(135deg, var(--accent) 0%, #7c3aed 100%);">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-value"><?= isset($activeStudents) ? (int) $activeStudents : '0' ?></div>
        <div class="stat-label">Active Students</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="stat-card shadow-sm" style="border-left-color: var(--info); cursor: pointer;" onclick="toggleSection('enrollmentsSection')" title="Click to view all enrollments">
        <div class="stat-icon" style="background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value"><?= isset($totalEnrollments) ? (int) $totalEnrollments : '0' ?></div>
        <div class="stat-label">Enrollments</div>
      </div>
    </div>
  </div>
  
  <!-- Archive Stat Card -->
  <?php 
    $totalArchived = (count($archivedUsers ?? []) + count($archivedCourses ?? []) + count($archivedEnrollments ?? []));
    if ($totalArchived > 0): 
  ?>
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card shadow-sm border-warning" style="cursor: pointer;" onclick="toggleSection('archiveSection')" title="Click to view archived items">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <i class="fas fa-archive text-warning me-2"></i>
              <strong>Archive</strong>
              <span class="badge bg-warning ms-2"><?= $totalArchived ?> item<?= $totalArchived !== 1 ? 's' : '' ?> archived</span>
            </div>
            <div class="text-muted">
              <small>Items will be permanently deleted after 30 days</small>
              <i class="fas fa-chevron-right ms-2"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Users Section -->
  <div class="card shadow-sm mb-4" id="usersSection" style="display: none;">
    <div class="card-header">
      <i class="fas fa-users me-2"></i>All Users
    </div>
    <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
      <?php if (!empty($allUsers)): ?>
        <!-- Search Filter -->
        <div class="mb-3">
          <form id="usersSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="usersSearchInput" name="search_term" class="form-control" placeholder="Search by username, email, role, or year level..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="clearUsersSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Year Level</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <?php $i = 1; foreach ($allUsers as $user): ?>
                <tr class="user-row">
                  <td><?= $i++ ?></td>
                  <td class="user-username"><?= esc($user['username']) ?></td>
                  <td class="user-email"><?= esc($user['email']) ?></td>
                  <td class="user-role">
                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'teacher' ? 'bg-primary' : 'bg-success') ?>">
                      <?= esc(ucfirst($user['role'])) ?>
                    </span>
                  </td>
                  <td class="user-status">
                    <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                      <?= esc(ucfirst($user['status'] ?? 'active')) ?>
                    </span>
                  </td>
                  <td class="user-year"><?= esc($user['year_level'] ?? '—') ?></td>
                  <td><small><?= $user['created_at'] ? date('M j, Y', strtotime($user['created_at'])) : '—' ?></small></td>
                  <td>
                    <button class="btn btn-outline-primary btn-sm edit-user-btn" data-user-id="<?= (int)$user['id'] ?>" data-username="<?= esc($user['username']) ?>" data-email="<?= esc($user['email']) ?>" data-year="<?= esc($user['year_level'] ?? '') ?>" data-role="<?= esc($user['role']) ?>" data-status="<?= esc($user['status'] ?? 'active') ?>">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <div class="btn-group">
                      <button class="btn btn-outline-danger btn-sm delete-user-btn" data-user-id="<?= (int)$user['id'] ?>" data-username="<?= esc($user['username']) ?>" data-force="false" title="Move to Archive">
                        <i class="fas fa-archive me-1"></i> Archive
                      </button>
                      <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item force-delete-user-btn" href="#" data-user-id="<?= (int)$user['id'] ?>" data-username="<?= esc($user['username']) ?>">
                          <i class="fas fa-trash text-danger me-1"></i> Force Delete
                        </a></li>
                      </ul>
                    </div>
                  </td>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div id="noUsersResults" class="text-center py-3" style="display: none;">
            <i class="fas fa-search text-muted"></i>
            <p class="text-muted mb-0 mt-2">No users found matching your search.</p>
          </div>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users"></i>
          <p class="mb-0">No users found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Active Students Section (Grouped by Year) -->
  <div class="card shadow-sm mb-4" id="activeStudentsSection" style="display: none;">
    <div class="card-header">
      <i class="fas fa-user-graduate me-2"></i>Active Students by Year Level
    </div>
    <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
      <?php 
        // Group active students by year level (excluding deleted)
        $activeStudentsByYear = [];
        if (!empty($allUsers)) {
          foreach ($allUsers as $user) {
            // Check if user is student, active, and not deleted
            if ($user['role'] === 'student' && ($user['status'] ?? 'active') === 'active' && empty($user['deleted_at'])) {
              $year = $user['year_level'] ?? 'No Year';
              if (!isset($activeStudentsByYear[$year])) {
                $activeStudentsByYear[$year] = [];
              }
              $activeStudentsByYear[$year][] = $user;
            }
          }
        }
      ?>
      <?php if (!empty($activeStudentsByYear)): ?>
        <!-- Search Filter -->
        <div class="mb-3">
          <form id="activeStudentsSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="activeStudentsSearchInput" name="search_term" class="form-control" placeholder="Search active students by username, email, or year level..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="clearActiveStudentsSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
        
        <?php foreach ($activeStudentsByYear as $year => $students): ?>
          <div class="mb-4 year-group" data-year="<?= esc($year) ?>">
            <h5 class="mb-3">
              <span class="badge bg-success me-2">Year <?= esc($year) ?></span>
              <small class="text-muted">(<?= count($students) ?> student<?= count($students) !== 1 ? 's' : '' ?>)</small>
            </h5>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody class="active-student-tbody">
                  <?php $i = 1; foreach ($students as $student): ?>
                    <tr class="active-student-row" data-student-id="<?= (int)$student['id'] ?>">
                      <td><?= $i++ ?></td>
                      <td class="active-student-username"><?= esc($student['username']) ?></td>
                      <td class="active-student-email"><?= esc($student['email']) ?></td>
                      <td>
                        <span class="badge bg-success">Active</span>
                      </td>
                      <td><small><?= $student['created_at'] ? date('M j, Y', strtotime($student['created_at'])) : '—' ?></small></td>
                      <td>
                        <button class="btn btn-outline-primary btn-sm edit-user-btn" data-user-id="<?= (int)$student['id'] ?>" data-username="<?= esc($student['username']) ?>" data-email="<?= esc($student['email']) ?>" data-year="<?= esc($student['year_level'] ?? '') ?>" data-role="<?= esc($student['role']) ?>" data-status="<?= esc($student['status'] ?? 'active') ?>">
                          <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <div class="btn-group">
                          <button class="btn btn-outline-danger btn-sm delete-user-btn" data-user-id="<?= (int)$student['id'] ?>" data-username="<?= esc($student['username']) ?>" data-force="false" title="Move to Archive">
                            <i class="fas fa-archive me-1"></i> Archive
                          </button>
                          <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                            <span class="visually-hidden">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item force-delete-user-btn" href="#" data-user-id="<?= (int)$student['id'] ?>" data-username="<?= esc($student['username']) ?>">
                              <i class="fas fa-trash text-danger me-1"></i> Force Delete
                            </a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endforeach; ?>
        <div id="noActiveStudentsResults" class="text-center py-3" style="display: none;">
          <i class="fas fa-search text-muted"></i>
          <p class="text-muted mb-0 mt-2">No active students found matching your search.</p>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-user-graduate"></i>
          <p class="mb-0">No active students found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Courses Section -->
  <div class="card shadow-sm mb-4" id="coursesSection" style="display: none;">
    <div class="card-header">
      <i class="fas fa-book me-2"></i>All Courses
    </div>
    <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
      <?php if (!empty($allCourses)): ?>
        <!-- Search Filter -->
        <div class="mb-3">
          <form id="coursesSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="coursesSearchInput" name="search_term" class="form-control" placeholder="Search by course title, description, instructor, or year level..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="clearCoursesSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Instructor</th>
                <th>Year Level</th>
                <th>Status</th>
                <th>Enrolled</th>
                <th>Capacity</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="coursesTableBody">
              <?php $i = 1; foreach ($allCourses as $course): ?>
                <tr class="course-row">
                  <td><?= $i++ ?></td>
                  <td class="course-title">
                    <strong><?= esc($course['title']) ?></strong>
                    <?php if (!empty($course['description'])): ?>
                      <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?><?= strlen($course['description']) > 50 ? '...' : '' ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="course-instructor"><?= esc($course['instructor_name'] ?? '—') ?></td>
                  <td class="course-year"><?= esc($course['year_level'] ?? '—') ?></td>
                  <td class="course-status">
                    <span class="badge <?= $course['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                      <?= esc(ucfirst($course['status'] ?? 'active')) ?>
                    </span>
                  </td>
                  <td><?= (int)($course['enrolled_count'] ?? 0) ?></td>
                  <td><?= (int)($course['capacity'] ?? 0) ?></td>
                  <td>
                    <button class="btn btn-outline-primary btn-sm edit-course-btn" data-course-id="<?= (int)$course['id'] ?>" data-title="<?= esc($course['title']) ?>" data-description="<?= esc($course['description'] ?? '') ?>" data-year="<?= esc($course['year_level'] ?? '') ?>" data-status="<?= esc($course['status'] ?? 'active') ?>" data-capacity="<?= (int)($course['capacity'] ?? 0) ?>">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <a href="<?= base_url('admin/course/' . (int)$course['id']) ?>" class="btn btn-outline-info btn-sm">
                      <i class="fas fa-eye me-1"></i> View
                    </a>
                    <div class="btn-group">
                      <button class="btn btn-outline-danger btn-sm delete-course-btn" data-course-id="<?= (int)$course['id'] ?>" data-title="<?= esc($course['title']) ?>" data-force="false" title="Move to Archive">
                        <i class="fas fa-archive me-1"></i> Archive
                      </button>
                      <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item force-delete-course-btn" href="#" data-course-id="<?= (int)$course['id'] ?>" data-title="<?= esc($course['title']) ?>">
                          <i class="fas fa-trash text-danger me-1"></i> Force Delete
                        </a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div id="noCoursesResults" class="text-center py-3" style="display: none;">
            <i class="fas fa-search text-muted"></i>
            <p class="text-muted mb-0 mt-2">No courses found matching your search.</p>
          </div>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-book"></i>
          <p class="mb-0">No courses found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Enrollments Section -->
  <div class="card shadow-sm mb-4" id="enrollmentsSection" style="display: none;">
    <div class="card-header">
      <i class="fas fa-chart-line me-2"></i>All Enrollments
    </div>
    <div class="card-body" style="background: transparent; padding: 1.5rem 1.5rem 0 1.5rem;">
      <?php if (!empty($allEnrollments)): ?>
        <!-- Search Filter -->
        <div class="mb-3">
          <form id="enrollmentsSearchForm" class="search-form" onsubmit="return false;">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input type="text" id="enrollmentsSearchInput" name="search_term" class="form-control" placeholder="Search by student name, email, course, or status..." autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" id="clearEnrollmentsSearchBtn" style="display: none;">
                <i class="fas fa-times"></i> Clear
              </button>
            </div>
          </form>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Status</th>
                <th>Enrollment Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="enrollmentsTableBody">
              <?php $i = 1; foreach ($allEnrollments as $enrollment): ?>
                <tr class="enrollment-row">
                  <td><?= $i++ ?></td>
                  <td class="enrollment-student">
                    <strong><?= esc($enrollment['student_username']) ?></strong>
                    <br><small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                  </td>
                  <td class="enrollment-course">
                    <a href="<?= base_url('admin/course/' . (int)$enrollment['course_id']) ?>" class="text-decoration-none">
                      <?= esc($enrollment['course_title']) ?>
                    </a>
                  </td>
                  <td class="enrollment-year"><?= esc($enrollment['student_year_level'] ?? '—') ?></td>
                  <td class="enrollment-status">
                    <span class="badge <?= $enrollment['approval_status'] === 'approved' ? 'bg-success' : ($enrollment['approval_status'] === 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                      <?= esc(ucfirst($enrollment['approval_status'] ?? 'pending')) ?>
                    </span>
                  </td>
                  <td><small><?= $enrollment['enrollment_date'] ? date('M j, Y g:i A', strtotime($enrollment['enrollment_date'])) : '—' ?></small></td>
                  <td>
                    <a href="<?= base_url('admin/course/' . (int)$enrollment['course_id']) ?>" class="btn btn-outline-info btn-sm">
                      <i class="fas fa-eye me-1"></i> View
                    </a>
                    <div class="btn-group">
                      <button class="btn btn-outline-danger btn-sm delete-enrollment-btn" data-enrollment-id="<?= (int)$enrollment['id'] ?>" data-student="<?= esc($enrollment['student_username']) ?>" data-course="<?= esc($enrollment['course_title']) ?>" data-force="false" title="Move to Archive">
                        <i class="fas fa-archive me-1"></i> Archive
                      </button>
                      <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item force-delete-enrollment-btn" href="#" data-enrollment-id="<?= (int)$enrollment['id'] ?>" data-student="<?= esc($enrollment['student_username']) ?>" data-course="<?= esc($enrollment['course_title']) ?>">
                          <i class="fas fa-trash text-danger me-1"></i> Force Delete
                        </a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div id="noEnrollmentsResults" class="text-center py-3" style="display: none;">
            <i class="fas fa-search text-muted"></i>
            <p class="text-muted mb-0 mt-2">No enrollments found matching your search.</p>
          </div>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-chart-line"></i>
          <p class="mb-0">No enrollments found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?= view('courses/modal_form') ?>
  <?= view('Admin/modal_form') ?>
  <?= view('Admin/teacher_modal') ?>
  <?= view('Admin/student_modal') ?>
  
  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editUserForm">
          <div class="modal-body">
            <input type="hidden" id="editUserId" name="user_id">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" id="editUsername" name="username" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select class="form-select" id="editRole" name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" id="editStatus" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Year Level</label>
              <input type="text" class="form-control" id="editYearLevel" name="year_level" placeholder="e.g., 1, 2, 3, 4">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Edit Course Modal -->
  <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editCourseForm">
          <div class="modal-body">
            <input type="hidden" id="editCourseId" name="course_id">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" id="editCourseTitle" name="title" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="editCourseDescription" name="description" rows="3"></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Year Level</label>
                <input type="text" class="form-control" id="editCourseYear" name="year_level" placeholder="e.g., 1, 2, 3, 4">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Capacity</label>
                <input type="number" class="form-control" id="editCourseCapacity" name="capacity" min="1">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" id="editCourseStatus" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Toggle sections when clicking stat cards
function toggleSection(sectionId) {
  var section = document.getElementById(sectionId);
  if (section) {
    if (section.style.display === 'none') {
      section.style.display = 'block';
      // Scroll to section
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      section.style.display = 'none';
    }
  }
}

// Admin Dashboard Search Filters - Pure JavaScript
(function() {
  // Active Students Search Filter
  function initActiveStudentsSearch() {
    var searchInput = document.getElementById('activeStudentsSearchInput');
    var clearBtn = document.getElementById('clearActiveStudentsSearchBtn');
    
    if (!searchInput) {
      setTimeout(initActiveStudentsSearch, 50);
      return;
    }
    
    function filterActiveStudents() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var rows = document.querySelectorAll('.active-student-row');
      var yearGroups = document.querySelectorAll('.year-group');
      var visibleCount = 0;
      var i;
      var noResultsMsg = document.getElementById('noActiveStudentsResults');
      
      for (i = 0; i < rows.length; i++) {
        var row = rows[i];
        var usernameEl = row.querySelector('.active-student-username');
        var emailEl = row.querySelector('.active-student-email');
        var yearGroup = row.closest('.year-group');
        var year = yearGroup ? yearGroup.getAttribute('data-year') : '';
        
        var usernameText = usernameEl ? usernameEl.textContent.toLowerCase() : '';
        var emailText = emailEl ? emailEl.textContent.toLowerCase() : '';
        var yearText = year.toLowerCase();
        
        var matches = searchTerm === '' || 
                     usernameText.indexOf(searchTerm) !== -1 ||
                     emailText.indexOf(searchTerm) !== -1 ||
                     yearText.indexOf(searchTerm) !== -1;
        
        if (matches) {
          row.style.display = '';
          if (yearGroup) yearGroup.style.display = 'block';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      }
      
      // Hide year groups with no visible students
      yearGroups.forEach(function(group) {
        var visibleRows = group.querySelectorAll('.active-student-row[style=""]');
        if (visibleRows.length === 0 && searchTerm !== '') {
          group.style.display = 'none';
        } else if (searchTerm === '') {
          group.style.display = 'block';
        }
      });
      
      if (clearBtn) {
        clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
      }
      
      if (noResultsMsg) {
        if (visibleCount === 0 && searchTerm.length > 0) {
          noResultsMsg.style.display = 'block';
        } else {
          noResultsMsg.style.display = 'none';
        }
      }
    }
    
    searchInput.addEventListener('input', filterActiveStudents);
    searchInput.addEventListener('keyup', filterActiveStudents);
    
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterActiveStudents();
      });
    }
  }
  
  // Users Search Filter
  function initUsersSearch() {
    var searchInput = document.getElementById('usersSearchInput');
    var clearBtn = document.getElementById('clearUsersSearchBtn');
    var tableBody = document.getElementById('usersTableBody');
    
    if (!searchInput || !tableBody) {
      setTimeout(initUsersSearch, 50);
      return;
    }
    
    function filterUsers() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var rows = tableBody.querySelectorAll('.user-row');
      var visibleCount = 0;
      var i;
      var noResultsMsg = document.getElementById('noUsersResults');
      
      for (i = 0; i < rows.length; i++) {
        var row = rows[i];
        var usernameEl = row.querySelector('.user-username');
        var emailEl = row.querySelector('.user-email');
        var roleEl = row.querySelector('.user-role');
        var yearEl = row.querySelector('.user-year');
        
        var usernameText = usernameEl ? usernameEl.textContent.toLowerCase() : '';
        var emailText = emailEl ? emailEl.textContent.toLowerCase() : '';
        var roleText = roleEl ? roleEl.textContent.toLowerCase() : '';
        var yearText = yearEl ? yearEl.textContent.toLowerCase() : '';
        
        var matches = searchTerm === '' || 
                     usernameText.indexOf(searchTerm) !== -1 ||
                     emailText.indexOf(searchTerm) !== -1 ||
                     roleText.indexOf(searchTerm) !== -1 ||
                     yearText.indexOf(searchTerm) !== -1;
        
        if (matches) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      }
      
      // Update row numbers
      var visibleRows = tableBody.querySelectorAll('.user-row[style=""]');
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
    
    searchInput.addEventListener('input', filterUsers);
    searchInput.addEventListener('keyup', filterUsers);
    
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterUsers();
      });
    }
  }
  
  // Courses Search Filter
  function initCoursesSearch() {
    var searchInput = document.getElementById('coursesSearchInput');
    var clearBtn = document.getElementById('clearCoursesSearchBtn');
    var tableBody = document.getElementById('coursesTableBody');
    
    if (!searchInput || !tableBody) {
      setTimeout(initCoursesSearch, 50);
      return;
    }
    
    function filterCourses() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var rows = tableBody.querySelectorAll('.course-row');
      var visibleCount = 0;
      var i;
      var noResultsMsg = document.getElementById('noCoursesResults');
      
      for (i = 0; i < rows.length; i++) {
        var row = rows[i];
        var titleEl = row.querySelector('.course-title');
        var instructorEl = row.querySelector('.course-instructor');
        var yearEl = row.querySelector('.course-year');
        
        var titleText = titleEl ? titleEl.textContent.toLowerCase() : '';
        var instructorText = instructorEl ? instructorEl.textContent.toLowerCase() : '';
        var yearText = yearEl ? yearEl.textContent.toLowerCase() : '';
        
        var matches = searchTerm === '' || 
                     titleText.indexOf(searchTerm) !== -1 ||
                     instructorText.indexOf(searchTerm) !== -1 ||
                     yearText.indexOf(searchTerm) !== -1;
        
        if (matches) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      }
      
      // Update row numbers
      var visibleRows = tableBody.querySelectorAll('.course-row[style=""]');
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
    
    searchInput.addEventListener('input', filterCourses);
    searchInput.addEventListener('keyup', filterCourses);
    
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterCourses();
      });
    }
  }
  
  // Enrollments Search Filter
  function initEnrollmentsSearch() {
    var searchInput = document.getElementById('enrollmentsSearchInput');
    var clearBtn = document.getElementById('clearEnrollmentsSearchBtn');
    var tableBody = document.getElementById('enrollmentsTableBody');
    
    if (!searchInput || !tableBody) {
      setTimeout(initEnrollmentsSearch, 50);
      return;
    }
    
    function filterEnrollments() {
      var searchTerm = searchInput.value.toLowerCase().trim();
      var rows = tableBody.querySelectorAll('.enrollment-row');
      var visibleCount = 0;
      var i;
      var noResultsMsg = document.getElementById('noEnrollmentsResults');
      
      for (i = 0; i < rows.length; i++) {
        var row = rows[i];
        var studentEl = row.querySelector('.enrollment-student');
        var courseEl = row.querySelector('.enrollment-course');
        var statusEl = row.querySelector('.enrollment-status');
        
        var studentText = studentEl ? studentEl.textContent.toLowerCase() : '';
        var courseText = courseEl ? courseEl.textContent.toLowerCase() : '';
        var statusText = statusEl ? statusEl.textContent.toLowerCase() : '';
        
        var matches = searchTerm === '' || 
                     studentText.indexOf(searchTerm) !== -1 ||
                     courseText.indexOf(searchTerm) !== -1 ||
                     statusText.indexOf(searchTerm) !== -1;
        
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
  
  // Initialize all searches
  function initAll() {
    initUsersSearch();
    initCoursesSearch();
    initEnrollmentsSearch();
    initActiveStudentsSearch();
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();

// CRUD Operations
jQuery(document).ready(function($) {
  // Use event delegation for dynamically loaded content
  $(document).on('click', '.edit-user-btn', function() {
    var userId = $(this).data('user-id');
    $('#editUserId').val(userId);
    $('#editUsername').val($(this).data('username'));
    $('#editEmail').val($(this).data('email'));
    $('#editRole').val($(this).data('role'));
    $('#editStatus').val($(this).data('status'));
    $('#editYearLevel').val($(this).data('year') || '');
    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
  });
  
  // Update User
  $('#editUserForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: '<?= base_url('admin/users/update') ?>',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response && response.success) {
          alert('User updated successfully!');
          location.reload();
        } else {
          alert('Error: ' + (response && response.message ? response.message : 'Failed to update user'));
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error, xhr.responseText);
        var errorMsg = 'An error occurred. Please try again.';
        try {
          var response = JSON.parse(xhr.responseText);
          if (response && response.message) {
            errorMsg = response.message;
          }
        } catch(e) {}
        alert(errorMsg);
      }
    });
  });
  
  // Delete User (Soft Delete - Archive)
  $(document).on('click', '.delete-user-btn', function() {
    var userId = $(this).data('user-id');
    var username = $(this).data('username');
    var force = $(this).data('force') === true || $(this).data('force') === 'true';
    
    var message = force 
      ? 'Are you sure you want to PERMANENTLY delete user "' + username + '"? This action CANNOT be undone!'
      : 'Move user "' + username + '" to archive? It can be restored within 30 days.';
    
    if (confirm(message)) {
      $.ajax({
        url: '<?= base_url('admin/users/delete/') ?>' + userId,
        method: 'POST',
        data: { force: force },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'User ' + (force ? 'permanently deleted' : 'moved to archive') + '!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete user'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          var errorMsg = 'An error occurred. Please try again.';
          try {
            var response = JSON.parse(xhr.responseText);
            if (response && response.message) {
              errorMsg = response.message;
            }
          } catch(e) {}
          alert(errorMsg);
        }
      });
    }
  });
  
  // Force Delete User
  $(document).on('click', '.force-delete-user-btn', function(e) {
    e.preventDefault();
    var userId = $(this).data('user-id');
    var username = $(this).data('username');
    if (confirm('Are you sure you want to PERMANENTLY delete user "' + username + '"? This action CANNOT be undone!')) {
      $.ajax({
        url: '<?= base_url('admin/users/delete/') ?>' + userId,
        method: 'POST',
        data: { force: true },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'User permanently deleted!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete user'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Restore User
  $(document).on('click', '.restore-user-btn', function() {
    var userId = $(this).data('user-id');
    var username = $(this).data('username');
    if (confirm('Restore user "' + username + '" from archive?')) {
      $.ajax({
        url: '<?= base_url('admin/users/restore/') ?>' + userId,
        method: 'POST',
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert('User restored successfully!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to restore user'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Edit Course
  $(document).on('click', '.edit-course-btn', function() {
    var courseId = $(this).data('course-id');
    $('#editCourseId').val(courseId);
    $('#editCourseTitle').val($(this).data('title') || '');
    $('#editCourseDescription').val($(this).data('description') || '');
    $('#editCourseYear').val($(this).data('year') || '');
    $('#editCourseStatus').val($(this).data('status') || 'active');
    $('#editCourseCapacity').val($(this).data('capacity') || '');
    var editModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
    editModal.show();
  });
  
  // Update Course
  $('#editCourseForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: '<?= base_url('admin/courses/update') ?>',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response && response.success) {
          alert('Course updated successfully!');
          location.reload();
        } else {
          alert('Error: ' + (response && response.message ? response.message : 'Failed to update course'));
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error, xhr.responseText);
        var errorMsg = 'An error occurred. Please try again.';
        try {
          var response = JSON.parse(xhr.responseText);
          if (response && response.message) {
            errorMsg = response.message;
          }
        } catch(e) {}
        alert(errorMsg);
      }
    });
  });
  
  // Delete Course (Soft Delete - Archive)
  $(document).on('click', '.delete-course-btn', function() {
    var courseId = $(this).data('course-id');
    var title = $(this).data('title');
    var force = $(this).data('force') === true || $(this).data('force') === 'true';
    
    var message = force 
      ? 'Are you sure you want to PERMANENTLY delete course "' + title + '"? This action CANNOT be undone!'
      : 'Move course "' + title + '" to archive? It can be restored within 30 days.';
    
    if (confirm(message)) {
      $.ajax({
        url: '<?= base_url('admin/courses/delete/') ?>' + courseId,
        method: 'POST',
        data: { force: force },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'Course ' + (force ? 'permanently deleted' : 'moved to archive') + '!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete course'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          var errorMsg = 'An error occurred. Please try again.';
          try {
            var response = JSON.parse(xhr.responseText);
            if (response && response.message) {
              errorMsg = response.message;
            }
          } catch(e) {}
          alert(errorMsg);
        }
      });
    }
  });
  
  // Force Delete Course
  $(document).on('click', '.force-delete-course-btn', function(e) {
    e.preventDefault();
    var courseId = $(this).data('course-id');
    var title = $(this).data('title');
    if (confirm('Are you sure you want to PERMANENTLY delete course "' + title + '"? This action CANNOT be undone!')) {
      $.ajax({
        url: '<?= base_url('admin/courses/delete/') ?>' + courseId,
        method: 'POST',
        data: { force: true },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'Course permanently deleted!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete course'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Restore Course
  $(document).on('click', '.restore-course-btn', function() {
    var courseId = $(this).data('course-id');
    var title = $(this).data('title');
    if (confirm('Restore course "' + title + '" from archive?')) {
      $.ajax({
        url: '<?= base_url('admin/courses/restore/') ?>' + courseId,
        method: 'POST',
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert('Course restored successfully!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to restore course'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Delete Enrollment (Soft Delete - Archive)
  $(document).on('click', '.delete-enrollment-btn', function() {
    var enrollmentId = $(this).data('enrollment-id');
    var student = $(this).data('student');
    var course = $(this).data('course');
    var force = $(this).data('force') === true || $(this).data('force') === 'true';
    
    var message = force 
      ? 'Are you sure you want to PERMANENTLY delete enrollment for "' + student + '" in "' + course + '"? This action CANNOT be undone!'
      : 'Move enrollment for "' + student + '" in "' + course + '" to archive? It can be restored within 30 days.';
    
    if (confirm(message)) {
      $.ajax({
        url: '<?= base_url('admin/enrollments/delete/') ?>' + enrollmentId,
        method: 'POST',
        data: { force: force },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'Enrollment ' + (force ? 'permanently deleted' : 'moved to archive') + '!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete enrollment'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          var errorMsg = 'An error occurred. Please try again.';
          try {
            var response = JSON.parse(xhr.responseText);
            if (response && response.message) {
              errorMsg = response.message;
            }
          } catch(e) {}
          alert(errorMsg);
        }
      });
    }
  });
  
  // Force Delete Enrollment
  $(document).on('click', '.force-delete-enrollment-btn', function(e) {
    e.preventDefault();
    var enrollmentId = $(this).data('enrollment-id');
    var student = $(this).data('student');
    var course = $(this).data('course');
    if (confirm('Are you sure you want to PERMANENTLY delete enrollment for "' + student + '" in "' + course + '"? This action CANNOT be undone!')) {
      $.ajax({
        url: '<?= base_url('admin/enrollments/delete/') ?>' + enrollmentId,
        method: 'POST',
        data: { force: true },
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'Enrollment permanently deleted!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to delete enrollment'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Restore Enrollment
  $(document).on('click', '.restore-enrollment-btn', function() {
    var enrollmentId = $(this).data('enrollment-id');
    if (confirm('Restore this enrollment from archive?')) {
      $.ajax({
        url: '<?= base_url('admin/enrollments/restore/') ?>' + enrollmentId,
        method: 'POST',
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert('Enrollment restored successfully!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to restore enrollment'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
  
  // Cleanup Old Deletes
  $('#cleanupBtn').on('click', function() {
    if (confirm('Permanently delete all archived items older than 30 days? This action cannot be undone!')) {
      $.ajax({
        url: '<?= base_url('admin/cleanup') ?>',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
          if (response && response.success) {
            alert(response.message || 'Cleanup completed!');
            location.reload();
          } else {
            alert('Error: ' + (response && response.message ? response.message : 'Failed to cleanup'));
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error, xhr.responseText);
          alert('An error occurred. Please try again.');
        }
      });
    }
  });
});
</script>