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
            'name' => 'participant_quota',
            'label' => 'Kuota Peserta',
            'type' => 'number',
            'value' => old('participant_quota', 0),
            'errors' => $errors['participant_quota'] ?? [],
            'attributes' => ['min' => 1, 'required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'committee_quota',
            'label' => 'Kuota Panitia',
            'type' => 'number',
            'value' => old('committee_quota', 0),
            'errors' => $errors['committee_quota'] ?? [],
            'attributes' => ['min' => 1, 'required' => true],
        ];
        include app_path('Views/partials/_form_field.php');
        ?>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="registration_start" class="mb-1 block text-sm font-medium text-slate-600">Mulai Pendaftaran</label>
                <input
                    type="datetime-local"
                    id="registration_start"
                    name="registration_start"
                    value="<?= e((string) old('registration_start', '')); ?>"
                    class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
                >
                <?php if (!empty($errors['registration_start'])): ?>
                    <p class="mt-1 text-xs text-rose-600"><?= e($errors['registration_start'][0]); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="registration_end" class="mb-1 block text-sm font-medium text-slate-600">Akhir Pendaftaran</label>
                <input
                    type="datetime-local"
                    id="registration_end"
                    name="registration_end"
                    value="<?= e((string) old('registration_end', '')); ?>"
                    class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
                >
                <?php if (!empty($errors['registration_end'])): ?>
                    <p class="mt-1 text-xs text-rose-600"><?= e($errors['registration_end'][0]); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <label for="status" class="mb-1 block text-sm font-medium text-slate-600">Status Open Recruitment</label>
            <select
                id="status"
                name="status"
                class="block w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring"
                required
            >
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status); ?>" <?= old('status', 'draft') === $status ? 'selected' : ''; ?>>
                        <?= ucfirst($status); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['status'])): ?>
                <p class="mt-1 text-xs text-rose-600"><?= e($errors['status'][0]); ?></p>
            <?php endif; ?>
        </div>

        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="/manage/events" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Simpan Event
            </button>
        </div>
    </form>
</section>
