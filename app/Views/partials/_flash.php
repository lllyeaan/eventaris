<?php
$success = flash('success');
$error = flash('error');
$errorMessages = flash('error_messages') ?? [];
$info = flash('info');
?>
<?php if ($success): ?>
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        <?= e((string) $success); ?>
    </div>
<?php endif; ?>

<?php if ($error || !empty($errorMessages)): ?>
    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <?php if ($error): ?>
            <p class="font-medium"><?= e((string) $error); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessages)): ?>
            <ul class="mt-2 list-disc pl-5">
                <?php foreach ((array) $errorMessages as $message): ?>
                    <li><?= e((string) $message); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($info): ?>
    <div class="mb-4 rounded border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
        <?= e((string) $info); ?>
    </div>
<?php endif; ?>
