<?php
$events = $events ?? [];
?>
<section class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Kelola Event</h1>
            <p class="text-sm text-slate-500">Buat, ubah, dan pantau kuota event yang sedang berjalan.</p>
        </div>
        <a href="/manage/events/create" class="inline-flex items-center gap-2 rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
            + Event Baru
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Event</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Tanggal</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Peserta (pending/approved)</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Panitia (pending/approved)</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-slate-500">Belum ada event yang dibuat.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-700"><?= e($event['name']); ?></div>
                                <div class="text-xs text-slate-500"><?= e($event['location']); ?></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?= !empty($event['event_date']) ? e(date('d M Y', strtotime((string) $event['event_date']))) : 'TBA'; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold uppercase"><?= e($event['status']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?= e((string) ($event['participants_pending'] ?? 0)); ?>
                                /
                                <?= e((string) ($event['participants_approved'] ?? 0)); ?>
                                dari <?= e((string) $event['participant_quota']); ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?= e((string) ($event['committees_pending'] ?? 0)); ?>
                                /
                                <?= e((string) ($event['committees_approved'] ?? 0)); ?>
                                dari <?= e((string) $event['committee_quota']); ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/manage/events/edit?id=<?= e((string) $event['id']); ?>" class="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Edit</a>
                                    <form action="/manage/events/delete" method="post" data-confirm="Hapus event ini beserta pendaftaran terkait?">
                                        <input type="hidden" name="id" value="<?= e((string) $event['id']); ?>">
                                        <button type="submit" class="rounded border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
