<?php
$events = $events ?? [];
$isLanding = $isLanding ?? false;
?>
<section class="space-y-8">
    <?php if ($isLanding): ?>
        <div class="rounded-2xl bg-gradient-to-r from-sky-600 to-indigo-600 p-8 text-white shadow">
            <h1 class="text-3xl font-bold">Kelola Event Kampus Lebih Mudah</h1>
            <p class="mt-3 max-w-2xl text-sm text-sky-100">
                Eventory membantu panitia mengelola open recruitment peserta dan panitia secara terstruktur,
                dengan alur pendaftaran yang jelas dan laporan ringkas untuk pengambilan keputusan.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="/events" class="rounded bg-white px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm hover:bg-slate-100">
                    Jelajahi Event
                </a>
                <a href="/register" class="rounded border border-white px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">
                    Mulai Buat Event
                </a>
            </div>
        </div>
    <?php else: ?>
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Daftar Event</h1>
            <p class="text-sm text-slate-500">Temukan event menarik dan ajukan diri sebagai peserta atau panitia.</p>
        </div>
    <?php endif; ?>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (empty($events)): ?>
            <div class="col-span-full rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center text-slate-500">
                Belum ada event yang tersedia saat ini.
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <article class="flex h-full flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800"><?= e($event['name']); ?></h2>
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs font-semibold uppercase text-slate-600"><?= e($event['status']); ?></span>
                        </div>
                        <p class="text-sm text-slate-600"><?= e(substr((string) $event['description'], 0, 150)); ?><?= strlen((string) $event['description']) > 150 ? '...' : ''; ?></p>
                    </div>
                    <dl class="mt-4 space-y-2 text-xs text-slate-500">
                        <?php if (!empty($event['event_date'])): ?>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-slate-600">Tanggal</span>
                                <span><?= e(date('d M Y', strtotime((string) $event['event_date']))); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-600">Lokasi</span>
                            <span><?= e($event['location']); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-600">Kuota Peserta</span>
                            <span><?= e((string) $event['participant_quota']); ?> orang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-600">Kuota Panitia</span>
                            <span><?= e((string) $event['committee_quota']); ?> orang</span>
                        </div>
                    </dl>
                    <div class="mt-4">
                        <a href="/events/show?id=<?= e((string) $event['id']); ?>" class="inline-flex items-center justify-center rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                            Lihat Detail
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
