<?php
$user = $user ?? [];
$errors = $errors ?? [];
?>
<section class="mx-auto max-w-xl space-y-6 rounded-xl bg-white p-6 shadow-sm">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Profil Saya</h1>
        <p class="text-sm text-slate-500">Perbarui nama tampilan atau ganti password akun Anda.</p>
    </div>

    <form action="/profile" method="post" class="space-y-4">
        <?php
        $field = [
            'name' => 'name',
            'label' => 'Nama Lengkap',
            'type' => 'text',
            'value' => old('name', $user['name'] ?? ''),
            'errors' => $errors['name'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');
        ?>

        <div class="rounded border border-slate-200 bg-slate-50 p-4">
            <h2 class="text-sm font-semibold text-slate-700">Informasi Login</h2>
            <p class="text-xs text-slate-500">Email tidak dapat diubah. Kosongkan password baru bila tidak ingin mengganti password.</p>
            <p class="mt-2 text-sm font-medium text-slate-600">Email: <span class="font-normal text-slate-500"><?= e($user['email'] ?? ''); ?></span></p>
        </div>

        <?php
        $field = [
            'name' => 'current_password',
            'label' => 'Password Saat Ini',
            'type' => 'password',
            'errors' => $errors['current_password'] ?? [],
            'help' => 'Wajib diisi jika ingin mengganti password.',
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'new_password',
            'label' => 'Password Baru',
            'type' => 'password',
            'errors' => $errors['new_password'] ?? [],
            'help' => 'Minimal 6 karakter.',
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'new_password_confirmation',
            'label' => 'Konfirmasi Password Baru',
            'type' => 'password',
            'errors' => $errors['new_password_confirmation'] ?? [],
        ];
        include app_path('Views/partials/_form_field.php');
        ?>

        <div class="flex items-center justify-end gap-2">
            <a href="/dashboard" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Simpan Perubahan</button>
        </div>
    </form>
</section>
