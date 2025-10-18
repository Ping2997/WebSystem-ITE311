<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CI Site</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-700: #2f6f65;
            --green-600: #3a7d6d;
            --green-500: #4f8f7f;
            --green-100: #eaf3f0;
        }
        .theme-navbar { background-color: var(--green-600); }
        .theme-navbar .navbar-brand,
        .theme-navbar .nav-link,
        .theme-navbar .navbar-toggler { color: #ffffff !important; }
        body { background-color: var(--green-100); }
    </style>
</head>
<body>
    <?= view('tempates/header') ?>

    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>
