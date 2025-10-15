<?php
use App\Core\Session;

$appName = config('app.name', 'Eventory');
$pageTitle = isset($title) ? $title . ' | ' . $appName : $appName;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">
</head>
<body class="bg-slate-100 min-h-screen">
    <?php include app_path('Views/partials/_navbar.php'); ?>

    <main class="max-w-6xl mx-auto px-4 py-6">
        <?php include app_path('Views/partials/_flash.php'); ?>
        <?= $content ?? ''; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-confirm]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const message = form.getAttribute('data-confirm') || 'Yakin ingin melanjutkan tindakan ini?';
                    if (!confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
