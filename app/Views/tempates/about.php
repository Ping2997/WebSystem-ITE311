<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    </head>
<body>
    <?= $this->include('tempates/header') ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center">
                <h1 class="display-5 text-green mb-4">
                    <i class="fas fa-info-circle me-2"></i>About
                </h1>
                <p class="text-muted"></p>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
