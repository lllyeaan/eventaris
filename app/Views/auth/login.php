<?php
$errors = $errors ?? [];
?>
<section class="mx-auto max-w-md rounded-lg bg-white p-6 shadow">
    <h1 class="mb-2 text-2xl font-semibold text-slate-800">Masuk</h1>
    <p class="mb-6 text-sm text-slate-500">Gunakan akun Anda untuk mengelola event dan pendaftaran.</p>
    <form action="/login" method="post" class="space-y-4">
        <?php
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
        ?>
        <button type="submit" class="w-full rounded bg-sky-600 py-2 text-sm font-semibold text-white hover:bg-sky-700">
            Login
        </button>
    </form>
    <p class="mt-4 text-center text-xs text-slate-500">
        Belum punya akun?
        <a href="/register" class="font-medium text-sky-600 hover:text-sky-700">Daftar sekarang</a>
    </p>
</section>
