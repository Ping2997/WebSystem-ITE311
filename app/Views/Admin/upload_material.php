<!DOCTYPE html>
<html>
<head>
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('tempates/header') ?>

    <main class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0 text-green"><i class="fa fa-upload me-2"></i>Upload Material for Course ID: <?= esc($course_id) ?></h3>
            <a href="<?= base_url('course/' . $course_id . '/materials') ?>" class="btn btn-outline-green btn-sm">
                <i class="fa fa-folder-open me-1"></i> View Materials
            </a>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php elseif(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('admin/course/' . $course_id . '/upload') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="material_file" class="form-label">Select File</label>
                        <input type="file" name="material_file" id="material_file" class="form-control" accept=".pdf,.ppt,.pptx" required>
                        <div class="form-text">Allowed types: PDF, PPT, PPTX. Max size: 10 MB.</div>
                    </div>
                    <button type="submit" class="btn btn-green">
                        <i class="fa fa-cloud-upload-alt me-1"></i> Upload
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
