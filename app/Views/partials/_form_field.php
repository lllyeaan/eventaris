<?php
use App\Core\Session;

$name = $field['name'] ?? '';
$label = $field['label'] ?? ucfirst(str_replace('_', ' ', $name));
$type = $field['type'] ?? 'text';
$value = $field['value'] ?? '';
$errors = $field['errors'] ?? [];
$attributes = $field['attributes'] ?? [];
$placeholder = $field['placeholder'] ?? '';
$help = $field['help'] ?? null;
$options = $field['options'] ?? [];
$isRequired = false;
$hasOldInput = Session::hasOldInput();
$valueIsFilled = $value !== null && $value !== '';
$hasErrors = !empty($errors);

$attrString = '';
foreach ($attributes as $attrKey => $attrValue) {
    if (is_bool($attrValue)) {
        if ($attrValue) {
            $attrString .= ' ' . $attrKey;
            if ($attrKey === 'required') {
                $isRequired = true;
            }
        }
        continue;
    }
    $attrString .= sprintf(' %s="%s"', $attrKey, htmlspecialchars((string) $attrValue, ENT_QUOTES, 'UTF-8'));
    if ($attrKey === 'required') {
        $isRequired = true;
    }
}

$inputClasses = 'block w-full rounded border px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring';
if ($hasErrors) {
    $inputClasses .= ' border-rose-400 focus:border-rose-500 focus:ring-rose-100';
} elseif ($hasOldInput && $valueIsFilled) {
    $inputClasses .= ' border-emerald-400 focus:border-emerald-500 focus:ring-emerald-100';
} else {
    $inputClasses .= ' border-slate-300 focus:border-sky-500 focus:ring-sky-100';
}
?>
<div class="mb-4">
    <label for="<?= e($name); ?>" class="mb-1 block text-sm font-medium text-slate-600">
        <?= e($label); ?>
        <?php if ($isRequired): ?>
        <span class="text-rose-500">*</span>
        <?php endif; ?>
    </label>
    <?php if ($type === 'textarea'): ?>
    <textarea id="<?= e($name); ?>" name="<?= e($name); ?>" class="<?= $inputClasses; ?>"
        placeholder="<?= e($placeholder); ?>" <?= $attrString; ?>><?= e((string) $value); ?></textarea>
    <?php elseif ($type === 'select'): ?>
    <select id="<?= e($name); ?>" name="<?= e($name); ?>" class="<?= $inputClasses; ?>" <?= $attrString; ?>>
        <?php foreach ($options as $optionValue => $optionLabel): ?>
            <option value="<?= e((string) $optionValue); ?>"
                <?= ((string) $value === (string) $optionValue) ? 'selected' : ''; ?>>
                <?= e((string) $optionLabel); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php else: ?>
    <input type="<?= e($type); ?>" id="<?= e($name); ?>" name="<?= e($name); ?>" value="<?= e((string) $value); ?>"
        class="<?= $inputClasses; ?>" placeholder="<?= e($placeholder); ?>" <?= $attrString; ?>>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
    <p class="mt-1 text-xs text-rose-600"><?= e($errors[0]); ?></p>
    <?php endif; ?>
    <?php if ($help): ?>
    <p class="mt-1 text-xs text-slate-500"><?= e($help); ?></p>
    <?php endif; ?>
</div>
