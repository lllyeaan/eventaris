<?php
$committee = $committee ?? null;
$statuses = $statuses ?? [];
?>
<?php if ($committee): ?>
    <section class="mx-auto max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800"><?= e($committee['full_name']); ?></h1>
                <p class="text-sm text-slate-500"><?= e($committee['event_name']); ?></p>
            </div>
            <span class="rounded bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600"><?= e($committee['status_label']); ?></span>
        </div>

        <dl class="grid gap-4 sm:grid-cols-2">
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Email</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($committee['email']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Telepon</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($committee['phone']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Institusi</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($committee['institution']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Divisi Utama</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e($committee['primary_division']); ?></dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Divisi Cadangan</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    <?= $committee['secondary_division'] !== '' ? e($committee['secondary_division']) : 'Tidak ada'; ?>
                </dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-xs font-semibold uppercase text-slate-500">Dibuat</dt>
                <dd class="mt-1 text-sm text-slate-700"><?= e(date('d M Y H:i', strtotime((string) $committee['created_at']))); ?></dd>
            </div>
        </dl>

        <div class="rounded border border-slate-200 p-4">
            <h2 class="text-sm font-semibold text-slate-600">Motivasi & Pengalaman</h2>
            <p class="mt-2 text-sm text-slate-600">
                <?= $committee['motivation'] !== '' ? nl2br(e($committee['motivation'])) : 'Tidak ada catatan.'; ?>
            </p>
        </div>

        <form action="/manage/committees/update-status" method="post" class="rounded border border-slate-200 p-4">
            <input type="hidden" name="id" value="<?= e((string) $committee['id']); ?>">
            <label for="status" class="mb-2 block text-sm font-medium text-slate-600">Perbarui Status</label>
            <select
                id="status"
                name="status"
                class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
                required
            >
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status['code']); ?>" <?= $committee['status_code'] === $status['code'] ? 'selected' : ''; ?>>
                        <?= e($status['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="mt-4 flex items-center justify-end gap-2">
                <a href="/manage/committees" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Kembali</a>
                <button type="submit" class="rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                    Simpan Status
                </button>
            </div>
        </form>
    </section>
<?php else: ?>
    <p class="text-center text-sm text-slate-500">Panitia tidak ditemukan.</p>
<?php endif; ?>
