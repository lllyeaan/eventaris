<?php
$stats = $stats ?? [];
$eventsSummary = $eventsSummary ?? [];
?>
<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500">Ringkasan aktivitas pendaftaran peserta dan panitia.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500">Total Event</h2>
            <p class="mt-2 text-3xl font-semibold text-slate-800"><?= e((string) ($stats['events'] ?? 0)); ?></p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500">Peserta Pending</h2>
            <p class="mt-2 text-3xl font-semibold text-amber-500"><?= e((string) ($stats['participants_pending'] ?? 0)); ?></p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500">Panitia Pending</h2>
            <p class="mt-2 text-3xl font-semibold text-indigo-500"><?= e((string) ($stats['committees_pending'] ?? 0)); ?></p>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="text-base font-semibold text-slate-700">Ketersediaan Slot Event</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Event</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Kuota Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Peserta Approved</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Sisa Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Kuota Panitia</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Panitia Approved</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Sisa Panitia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($eventsSummary)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-slate-500">Belum ada event yang terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventsSummary as $item): ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700"><?= e($item['name']); ?></td>
                                <td class="px-4 py-3 capitalize text-slate-500">
                                    <span class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold uppercase"><?= e($item['status']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['participant_quota']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['participants_approved']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['participants_remaining']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['committee_quota']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['committees_approved']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e((string) $item['committees_remaining']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
