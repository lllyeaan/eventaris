<?php
$errors = $errors ?? [];
$statuses = $statuses ?? [];
?>
<section class="mx-auto max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Tambah Event Baru</h1>
        <p class="text-sm text-slate-500">Lengkapi informasi dasar dan kuota untuk membuka pendaftaran.</p>
    </div>

    <form action="/manage/events" method="post">
        <?php
        $field = [
            'name' => 'name',
            'label' => 'Nama Event',
            'type' => 'text',
            'value' => old('name', ''),
            'errors' => $errors['name'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'description',
            'label' => 'Deskripsi / Rules',
            'type' => 'textarea',
            'value' => old('description', ''),
            'errors' => $errors['description'] ?? [],
            'attributes' => ['rows' => 6, 'required' => true],
            'help' => 'Minimal 10 karakter.',
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'location',
            'label' => 'Lokasi',
            'type' => 'text',
            'value' => old('location', ''),
            'errors' => $errors['location'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'event_date',
            'label' => 'Tanggal Event',
            'type' => 'date',
            'value' => old('event_date', ''),
            'errors' => $errors['event_date'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'committee_divisions',
            'label' => 'Daftar Divisi Panitia',
            'type' => 'textarea',
            'value' => old('committee_divisions', ''),
            'errors' => $errors['committee_divisions'] ?? [],
            'attributes' => ['rows' => 4],
            'help' => 'Pisahkan divisi dengan koma atau baris baru.',
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'participant_quota',
            'label' => 'Kuota Peserta',
            'type' => 'number',
            'value' => old('participant_quota', 1),
            'errors' => $errors['participant_quota'] ?? [],
            'attributes' => ['min' => 1, 'required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'committee_quota',
            'label' => 'Kuota Panitia',
            'type' => 'number',
            'value' => old('committee_quota', 1),
            'errors' => $errors['committee_quota'] ?? [],
            'attributes' => ['min' => 1, 'required' => true],
        ];
        include app_path('Views/partials/_form_field.php');
        ?>

        <div class="grid gap-4 md:grid-cols-2">
            <?php
            $field = [
                'name' => 'registration_start',
                'label' => 'Mulai Pendaftaran',
                'type' => 'datetime-local',
                'value' => old('registration_start', ''),
                'errors' => $errors['registration_start'] ?? [],
            ];
            include app_path('Views/partials/_form_field.php');

            $field = [
                'name' => 'registration_end',
                'label' => 'Akhir Pendaftaran',
                'type' => 'datetime-local',
                'value' => old('registration_end', ''),
                'errors' => $errors['registration_end'] ?? [],
            ];
            include app_path('Views/partials/_form_field.php');
            ?>
        </div>

        <?php
        $statusOptions = [];
        foreach ($statuses as $status) {
            $statusOptions[$status] = ucfirst($status);
        }
        $field = [
            'name' => 'status',
            'label' => 'Status Open Recruitment',
            'type' => 'select',
            'value' => old('status', 'draft'),
            'errors' => $errors['status'] ?? [],
            'attributes' => ['required' => true],
            'options' => $statusOptions,
        ];
        include app_path('Views/partials/_form_field.php');
        ?>

        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="/manage/events"
                class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit"
                class="rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Simpan Event
            </button>
        </div>
    </form>
</section>
