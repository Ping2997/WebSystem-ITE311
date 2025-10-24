    <nav class="navbar navbar-expand-lg navbar-dark theme-navbar" style="background-color:#3a7d6d;">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-graduation-cap me-2"></i>ITE311 LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') ?>">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationsDropdown" style="min-width: 320px; max-width: 360px;">
                                <li class="px-2 py-1"><strong>Notifications</strong></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <div id="notificationsList" class="d-flex flex-column gap-2" style="max-height: 360px; overflow-y: auto;">
                                        <div class="text-muted small px-2">No notifications</div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= session()->get('first_name') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- jQuery (needed for AJAX notifications) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        .notif-item { border: 1px solid #e6ecea; border-left: 4px solid #3a7d6d; background: #f8fbfa; padding: .5rem .75rem; border-radius: .35rem; }
        .notif-time { color: #6c757d; }
        .notif-actions .btn { border-color: #3a7d6d; color: #3a7d6d; }
        .notif-actions .btn:hover { background: #3a7d6d; color: #fff; }
    </style>

    <script>
        (function() {
            function renderNotifications(data) {
                var count = data.count || 0;
                var list = data.notifications || [];

                var $badge = $('#notificationBadge');
                if (count > 0) { $badge.text(count).removeClass('d-none'); } else { $badge.addClass('d-none').text('0'); }

                var $list = $('#notificationsList');
                $list.empty();
                if (!list.length) {
                    $list.append('<div class="notif-item d-flex align-items-center"><i class="fas fa-check-circle me-2" style="color:#3a7d6d"></i><div><div class="fw-semibold">You\'re all caught up</div><div class="small notif-time">No new notifications</div></div></div>');
                    return;
                }

                list.forEach(function(n) {
                    var safeMsg = $('<div>').text(n.message).html();
                    var createdAt = n.created_at ? new Date(n.created_at).toLocaleString() : '';
                    var item = [
                        '<div class="notif-item">',
                        '  <div class="me-2">',
                        '    <div class="fw-semibold">' + safeMsg + '</div>',
                        '    <div class="small notif-time">' + createdAt + '</div>',
                        '  </div>',
                        '  <div class="notif-actions mt-2 text-center">',
                        '    <button type="button" class="btn btn-sm btn-outline-success mark-read-btn" data-id="' + n.id + '">Mark as Read</button>',
                        '  </div>',
                        '</div>'
                    ].join('');
                    $list.append(item);
                });
            }

            function fetchNotifications() {
                var url = '<?= base_url('notifications') ?>' + '?_=' + Date.now();
                $.get(url).done(function(res){ renderNotifications(res); });
            }

            function markAsRead(id) {
                var url = '<?= base_url('notifications/mark_read') ?>/' + id + '?_=' + Date.now();
                return $.post(url).done(function(res){ if(res && res.status === 'success'){ fetchNotifications(); } });
            }

            $(document).ready(function() {
                fetchNotifications();
                setInterval(fetchNotifications, 60000);

                $(document).on('click', '.mark-read-btn', function(e){ e.preventDefault(); var id=$(this).data('id'); if(id){ markAsRead(id); }});

                // Also fetch when opening the dropdown to ensure latest
                $(document).on('show.bs.dropdown', '#notificationsDropdown', function () {
                    var $list = $('#notificationsList');
                    $list.html('<div class="text-center py-2"><div class="spinner-border spinner-border-sm" role="status"></div><span class="ms-2 small">Loading...</span></div>');
                    fetchNotifications();
                });
            });
        })();
    </script>