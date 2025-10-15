<?php
$name = $field['name'] ?? '';
$label = $field['label'] ?? ucfirst(str_replace('_', ' ', $name));
$type = $field['type'] ?? 'text';
$value = $field['value'] ?? '';
$errors = $field['errors'] ?? [];
$attributes = $field['attributes'] ?? [];
$placeholder = $field['placeholder'] ?? '';
$help = $field['help'] ?? null;

$attrString = '';
foreach ($attributes as $attrKey => $attrValue) {
    if (is_bool($attrValue)) {
        if ($attrValue) {
            $attrString .= ' ' . $attrKey;
        }
        continue;
    }
    $attrString .= sprintf(' %s="%s"', $attrKey, htmlspecialchars((string) $attrValue, ENT_QUOTES, 'UTF-8'));
}

$inputClasses = 'block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring';
if (!empty($errors)) {
    $inputClasses .= ' border-rose-400';
}
?>
<div class="mb-4">
    <label for="<?= e($name); ?>" class="mb-1 block text-sm font-medium text-slate-600"><?= e($label); ?></label>
    <?php if ($type === 'textarea'): ?>
        <textarea
            id="<?= e($name); ?>"
            name="<?= e($name); ?>"
            class="<?= $inputClasses; ?>"
            placeholder="<?= e($placeholder); ?>"
            <?= $attrString; ?>
        ><?= e((string) $value); ?></textarea>
    <?php else: ?>
        <input
            type="<?= e($type); ?>"
            id="<?= e($name); ?>"
            name="<?= e($name); ?>"
            value="<?= e((string) $value); ?>"
            class="<?= $inputClasses; ?>"
            placeholder="<?= e($placeholder); ?>"
            <?= $attrString; ?>
        >
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <p class="mt-1 text-xs text-rose-600"><?= e($errors[0]); ?></p>
    <?php endif; ?>
    <?php if ($help): ?>
        <p class="mt-1 text-xs text-slate-500"><?= e($help); ?></p>
    <?php endif; ?>
</div>
