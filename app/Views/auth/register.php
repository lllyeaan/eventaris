<?php
$errors = $errors ?? [];
?>
<section class="mx-auto max-w-lg rounded-lg bg-white p-6 shadow">
    <h1 class="mb-2 text-2xl font-semibold text-slate-800">Daftar Akun Baru</h1>
    <p class="mb-6 text-sm text-slate-500">Buat akun panitia untuk mulai mengelola event dan aplikasinya.</p>
    <form action="/register" method="post" class="space-y-4">
        <?php
        $field = [
            'name' => 'name',
            'label' => 'Nama Lengkap',
            'type' => 'text',
            'value' => old('name', ''),
            'errors' => $errors['name'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'value' => old('email', ''),
            'errors' => $errors['email'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'password',
            'label' => 'Password',
            'type' => 'password',
            'errors' => $errors['password'] ?? [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');

        $field = [
            'name' => 'password_confirmation',
            'label' => 'Konfirmasi Password',
            'type' => 'password',
            'errors' => [],
            'attributes' => ['required' => true],
        ];
        include app_path('Views/partials/_form_field.php');
        ?>
        <button type="submit" class="w-full rounded bg-sky-600 py-2 text-sm font-semibold text-white hover:bg-sky-700">
            Daftar
        </button>
    </form>
    <p class="mt-4 text-center text-xs text-slate-500">
        Sudah punya akun?
        <a href="/login" class="font-medium text-sky-600 hover:text-sky-700">Masuk di sini</a>
    </p>
</section>
