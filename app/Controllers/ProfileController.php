<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class ProfileController
{
    public function edit(): void
    {
        $userId = (int) Session::get('user_id');
        $user = User::find($userId);

        if ($user === null) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            Response::redirect('/login');
        }

        $errors = flash('errors') ?? [];

        view('profile/edit', [
            'title' => 'Profil Saya',
            'user' => $user,
            'errors' => $errors,
        ]);
    }

    public function update(): void
    {
        $userId = (int) Session::get('user_id');
        $user = User::find($userId);

        if ($user === null) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            Response::redirect('/login');
        }

        $input = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'current_password' => (string) ($_POST['current_password'] ?? ''),
            'new_password' => (string) ($_POST['new_password'] ?? ''),
            'new_password_confirmation' => (string) ($_POST['new_password_confirmation'] ?? ''),
        ];

        $validator = new Validator();
        $validator->validate($input, [
            'name' => 'required|min:3|max:150',
        ]);

        $errors = $validator->errors();

        $changingPassword = $input['new_password'] !== '';
        if ($changingPassword) {
            if ($input['new_password'] !== $input['new_password_confirmation']) {
                $errors['new_password'][] = 'Konfirmasi password baru tidak sesuai.';
            }

            if (strlen($input['new_password']) < 6) {
                $errors['new_password'][] = 'Password baru minimal 6 karakter.';
            }

            if (!password_verify($input['current_password'], $user['password'])) {
                $errors['current_password'][] = 'Password saat ini tidak sesuai.';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Periksa kembali data profil Anda.');
            Session::flash('error_messages', $this->formatErrors($errors));
            Session::flashInput([
                'name' => $input['name'],
            ]);
            Response::redirect('/profile');
        }

        $payload = ['name' => $input['name']];
        if ($changingPassword) {
            $payload['password'] = password_hash($input['new_password'], PASSWORD_DEFAULT);
        }

        User::updateProfile($userId, $payload);

        Session::put('user_name', $input['name']);
        Session::flash('success', 'Profil berhasil diperbarui.');
        Response::redirect('/profile');
    }

    private function formatErrors(array $errors): array
    {
        $messages = [];
        $labels = [
            'name' => 'Nama',
            'current_password' => 'Password saat ini',
            'new_password' => 'Password baru',
        ];

        foreach ($errors as $field => $fieldErrors) {
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            foreach ($fieldErrors as $message) {
                $messages[] = $label . ': ' . $message;
            }
        }

        return $messages;
    }
}
