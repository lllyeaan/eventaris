<?php
$participant = $participant ?? null;
$statuses = $statuses ?? [];
?>
<?php if ($participant): ?>
    <section class="mx-auto max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800"><?= e($participant['full_name']); ?></h1>
                <p class="text-sm text-slate-500"><?= e($participant['event_name']); ?></p>
            </div>
            <span class="rounded bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600"><?= e($participant['status_label']); ?></span>
        </div>

        <dl class="grid gap-4 sm:grid-cols-2">
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Email</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($participant['email']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Telepon</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($participant['phone']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Institusi</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($participant['institution']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Dibuat</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e(date('d M Y H:i', strtotime((string) $participant['created_at']))); ?></dd>
            </div>
        </dl>

        <div class="rounded border border-slate-200 p-4">
            <h2 class="text-sm font-semibold text-slate-600">Catatan / Alasan</h2>
            <p class="mt-2 text-sm text-slate-600">
                <?= $participant['notes'] !== '' ? nl2br(e($participant['notes'])) : 'Tidak ada catatan tambahan.'; ?>
            </p>
        </div>

        <form action="/manage/participants/update-status" method="post" class="rounded border border-slate-200 p-4">
            <input type="hidden" name="id" value="<?= e((string) $participant['id']); ?>">
            <label for="status" class="mb-2 block text-sm font-medium text-slate-600">Perbarui Status</label>
            <select
                id="status"
                name="status"
                class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
                required
            >
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status['code']); ?>" <?= $participant['status_code'] === $status['code'] ? 'selected' : ''; ?>>
                        <?= e($status['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="mt-4 flex items-center justify-end gap-2">
                <a href="/manage/participants" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Kembali</a>
                <button type="submit" class="rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                    Simpan Status
                </button>
            </div>
        </form>
    </section>
<?php else: ?>
    <p class="text-center text-sm text-slate-500">Peserta tidak ditemukan.</p>
<?php endif; ?>
