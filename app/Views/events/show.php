<?php
$event = $event ?? null;
$isOpen = $isOpen ?? false;
$participantSummary = $participantSummary ?? ['total' => 0, 'approved' => 0, 'pending' => 0];
$committeeSummary = $committeeSummary ?? ['total' => 0, 'approved' => 0, 'pending' => 0];
$participantErrors = flash('participant_errors') ?? [];
$committeeErrors = flash('committee_errors') ?? [];
$participantRemaining = max(0, (int) ($event['participant_quota'] ?? 0) - (int) ($participantSummary['approved'] ?? 0));
$committeeRemaining = max(0, (int) ($event['committee_quota'] ?? 0) - (int) ($committeeSummary['approved'] ?? 0));
$committeeDivisions = [];
if (is_array($event) && !empty($event['committee_divisions'])) {
    $committeeDivisions = array_values(array_filter(array_map(
        'trim',
        preg_split('/\r\n|\r|\n/', (string) $event['committee_divisions']) ?: []
    )));
}
?>
<?php if ($event): ?>
    <section class="space-y-8">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-800"><?= e($event['name']); ?></h1>
                    <p class="text-sm text-slate-500"><?= e($event['location']); ?></p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase <?= $isOpen ? 'border-emerald-300 bg-emerald-50 text-emerald-600' : 'border-slate-200 bg-slate-100 text-slate-600'; ?>">
                    <?= $isOpen ? 'Open Recruitment' : 'Closed'; ?>
                </span>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-slate-600"><?= nl2br(e($event['description'])); ?></p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded border border-slate-200 p-4">
                    <dt class="text-xs font-semibold uppercase text-slate-500">Tanggal Event</dt>
                    <dd class="mt-1 text-sm text-slate-700">
                        <?= !empty($event['event_date']) ? e(date('d M Y', strtotime((string) $event['event_date']))) : 'TBA'; ?>
                    </dd>
                </div>
                <div class="rounded border border-slate-200 p-4">
                    <dt class="text-xs font-semibold uppercase text-slate-500">Periode Pendaftaran</dt>
                    <dd class="mt-1 text-sm text-slate-700">
                        <?php if (!empty($event['registration_start']) && !empty($event['registration_end'])): ?>
                            <?= e(date('d M Y H:i', strtotime((string) $event['registration_start']))); ?>
                            &mdash;
                            <?= e(date('d M Y H:i', strtotime((string) $event['registration_end']))); ?>
                        <?php else: ?>
                            Tidak ditentukan
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
            <?php if (!empty($committeeDivisions)): ?>
                <div class="mt-4 rounded border border-slate-200 p-4">
                    <h2 class="text-sm font-semibold uppercase text-slate-500">Divisi Panitia</h2>
                    <ul class="mt-2 list-disc pl-5 text-sm text-slate-600">
                        <?php foreach ($committeeDivisions as $division): ?>
                            <li><?= e($division); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Pendaftaran Peserta</h2>
                <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
                    <span>Kuota: <?= e((string) ($event['participant_quota'] ?? 0)); ?></span>
                    <span>Pending: <?= e((string) ($participantSummary['pending'] ?? 0)); ?></span>
                    <span>Approved: <?= e((string) ($participantSummary['approved'] ?? 0)); ?></span>
                    <span>Sisa: <?= e((string) $participantRemaining); ?></span>
                </div>
                <?php if (!$isOpen): ?>
                    <p class="mt-4 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                        Periode pendaftaran peserta sudah ditutup.
                    </p>
                <?php elseif ($participantRemaining <= 0): ?>
                    <p class="mt-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        Kuota peserta sudah terpenuhi.
                    </p>
                <?php else: ?>
                    <form action="/events/apply-participant" method="post" class="mt-4">
                        <input type="hidden" name="event_id" value="<?= e((string) $event['id']); ?>">
                        <?php
                        $field = [
                            'name' => 'participant_full_name',
                            'label' => 'Nama Lengkap',
                            'type' => 'text',
                            'value' => old('participant_full_name', ''),
                            'errors' => $participantErrors['participant_full_name'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'participant_email',
                            'label' => 'Email',
                            'type' => 'email',
                            'value' => old('participant_email', ''),
                            'errors' => $participantErrors['participant_email'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'participant_phone',
                            'label' => 'Nomor HP/WA',
                            'type' => 'text',
                            'value' => old('participant_phone', ''),
                            'errors' => $participantErrors['participant_phone'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'participant_institution',
                            'label' => 'Institusi',
                            'type' => 'text',
                            'value' => old('participant_institution', ''),
                            'errors' => $participantErrors['participant_institution'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'participant_notes',
                            'label' => 'Catatan Tambahan',
                            'type' => 'textarea',
                            'value' => old('participant_notes', ''),
                            'errors' => $participantErrors['participant_notes'] ?? [],
                            'attributes' => ['rows' => 3],
                            'help' => 'Boleh kosong, tambahkan informasi penting bila perlu.',
                        ];
                        include app_path('Views/partials/_form_field.php');
                        ?>
                        <button type="submit" class="w-full rounded bg-emerald-600 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Kirim Pendaftaran Peserta
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Pendaftaran Panitia</h2>
                <?php if (!empty($committeeDivisions)): ?>
                    <p class="mt-2 text-xs text-slate-500">
                        Divisi tersedia:
                        <?php foreach ($committeeDivisions as $division): ?>
                            <span class="mr-1 inline-block rounded bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600"><?= e($division); ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>
                <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
                    <span>Kuota: <?= e((string) ($event['committee_quota'] ?? 0)); ?></span>
                    <span>Pending: <?= e((string) ($committeeSummary['pending'] ?? 0)); ?></span>
                    <span>Approved: <?= e((string) ($committeeSummary['approved'] ?? 0)); ?></span>
                    <span>Sisa: <?= e((string) $committeeRemaining); ?></span>
                </div>
                <?php if (!$isOpen): ?>
                    <p class="mt-4 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                        Periode pendaftaran panitia sudah ditutup.
                    </p>
                <?php elseif ($committeeRemaining <= 0): ?>
                    <p class="mt-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        Kuota panitia sudah terpenuhi.
                    </p>
                <?php else: ?>
                    <form action="/events/apply-committee" method="post" class="mt-4">
                        <input type="hidden" name="event_id" value="<?= e((string) $event['id']); ?>">
                        <?php
                        $field = [
                            'name' => 'committee_full_name',
                            'label' => 'Nama Lengkap',
                            'type' => 'text',
                            'value' => old('committee_full_name', ''),
                            'errors' => $committeeErrors['committee_full_name'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_email',
                            'label' => 'Email',
                            'type' => 'email',
                            'value' => old('committee_email', ''),
                            'errors' => $committeeErrors['committee_email'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_phone',
                            'label' => 'Nomor HP/WA',
                            'type' => 'text',
                            'value' => old('committee_phone', ''),
                            'errors' => $committeeErrors['committee_phone'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_institution',
                            'label' => 'Institusi',
                            'type' => 'text',
                            'value' => old('committee_institution', ''),
                            'errors' => $committeeErrors['committee_institution'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_primary_division',
                            'label' => 'Divisi Utama',
                            'type' => 'text',
                            'value' => old('committee_primary_division', ''),
                            'errors' => $committeeErrors['committee_primary_division'] ?? [],
                            'attributes' => ['required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_secondary_division',
                            'label' => 'Divisi Cadangan',
                            'type' => 'text',
                            'value' => old('committee_secondary_division', ''),
                            'errors' => $committeeErrors['committee_secondary_division'] ?? [],
                            'help' => 'Opsional, pilih divisi alternatif.',
                        ];
                        include app_path('Views/partials/_form_field.php');

                        $field = [
                            'name' => 'committee_motivation',
                            'label' => 'Motivasi & Pengalaman',
                            'type' => 'textarea',
                            'value' => old('committee_motivation', ''),
                            'errors' => $committeeErrors['committee_motivation'] ?? [],
                            'attributes' => ['rows' => 4, 'required' => true],
                        ];
                        include app_path('Views/partials/_form_field.php');
                        ?>
                        <button type="submit" class="w-full rounded bg-indigo-600 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Kirim Aplikasi Panitia
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <p class="text-center text-sm text-slate-500">Event tidak ditemukan.</p>
<?php endif; ?>
