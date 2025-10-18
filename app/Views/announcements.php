<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">Announcements</h1>

        <?php if (!empty($announcements)): ?>
            <div class="list-group">
                <?php foreach ($announcements as $a): ?>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?= esc($a['title']) ?></h5>
                            <small class="text-muted"><?= esc(date('M d, Y h:i A', strtotime($a['created_at']))) ?></small>
                        </div>
                        <p class="mb-1"><?= esc($a['content']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No announcements available.</div>
        <?php endif; ?>

        <div class="mt-4">
            <a class="btn btn-secondary" href="/">Back to Home</a>
        </div>
    </div>
</body>
</html>
