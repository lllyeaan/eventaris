<?php
$success = flash('success');
$error = flash('error');
$info = flash('info');
?>
<?php if ($success): ?>
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        <?= e((string) $success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <?= e((string) $error); ?>
    </div>
<?php endif; ?>

<?php if ($info): ?>
    <div class="mb-4 rounded border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
        <?= e((string) $info); ?>
    </div>
<?php endif; ?>
