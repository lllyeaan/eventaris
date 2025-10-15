<?php
$participants = $participants ?? [];
$events = $events ?? [];
$statuses = $statuses ?? [];
$selectedEvent = $selectedEvent ?? null;
$selectedStatus = $selectedStatus ?? null;
?>
<section class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Kelola Pendaftaran Peserta</h1>
            <p class="text-sm text-slate-500">Tinjau dan kelola status peserta yang mendaftar ke event.</p>
        </div>
    </div>

    <form method="get" action="/manage/participants" class="grid gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3">
        <div>
            <label for="event_id" class="mb-1 block text-sm font-medium text-slate-600">Filter Event</label>
            <select
                id="event_id"
                name="event_id"
                class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
            >
                <option value="">Semua Event</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= e((string) $event['id']); ?>" <?= (string) $selectedEvent === (string) $event['id'] ? 'selected' : ''; ?>>
                        <?= e($event['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-slate-600">Status</label>
            <select
                id="status"
                name="status"
                class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
            >
                <option value="">Semua Status</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status['code']); ?>" <?= $selectedStatus === $status['code'] ? 'selected' : ''; ?>>
                        <?= e($status['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Terapkan Filter
            </button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Peserta</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Event</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Telepon</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($participants)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-slate-500">Belum ada pendaftaran peserta.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-700"><?= e($participant['full_name']); ?></div>
                                <div class="text-xs text-slate-500"><?= e($participant['institution']); ?></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= e($participant['event_name']); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= e($participant['email']); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= e($participant['phone']); ?></td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold uppercase"><?= e($participant['status_label']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/manage/participants/show?id=<?= e((string) $participant['id']); ?>" class="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Detail</a>
                                    <form action="/manage/participants/delete" method="post" data-confirm="Hapus pendaftaran peserta ini?">
                                        <input type="hidden" name="id" value="<?= e((string) $participant['id']); ?>">
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
