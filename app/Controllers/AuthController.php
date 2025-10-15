<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        if (Session::has('user_id')) {
            Response::redirect('/dashboard');
        }

        $errors = flash('errors') ?? [];

        view('auth/login', [
            'title' => 'Masuk',
            'errors' => $errors,
        ]);
    }

    public function login(): void
    {
        $input = [
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
        ];

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$isValid) {
            Session::flash('errors', $validator->errors());
            Session::flash('error', 'Periksa kembali data yang Anda masukkan.');
            Session::flashInput(['email' => $input['email']]);
            Response::redirect('/login');
        }

        $user = User::findByEmail($input['email']);

        if (!$user || !password_verify($input['password'], $user['password'])) {
            Session::flash('error', 'Email atau password tidak sesuai.');
            Session::flashInput(['email' => $input['email']]);
            Response::redirect('/login');
        }

        Session::regenerate();
        Session::put('user_id', $user['id']);
        Session::put('user_name', $user['name']);
        Session::put('user_email', $user['email']);

        Session::flash('success', 'Selamat datang kembali, ' . $user['name'] . '!');
        Response::redirect('/dashboard');
    }

    public function showRegister(): void
    {
        if (Session::has('user_id')) {
            Response::redirect('/dashboard');
        }

        $errors = flash('errors') ?? [];

        view('auth/register', [
            'title' => 'Daftar',
            'errors' => $errors,
        ]);
    }

    public function register(): void
    {
        $input = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
        ];

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$isValid) {
            Session::flash('errors', $validator->errors());
            Session::flash('error', 'Periksa kembali data pendaftaran Anda.');
            Session::flashInput([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
            Response::redirect('/register');
        }

        if ($input['password'] !== $input['password_confirmation']) {
            Session::flash('error', 'Konfirmasi password tidak sesuai.');
            Session::flashInput([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
            Response::redirect('/register');
        }

        if (User::findByEmail($input['email']) !== null) {
            Session::flash('error', 'Email sudah terdaftar. Silakan login.');
            Session::flashInput([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
            Response::redirect('/register');
        }

        $userId = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
        ]);

        Session::regenerate();
        Session::put('user_id', $userId);
        Session::put('user_name', $input['name']);
        Session::put('user_email', $input['email']);

        Session::flash('success', 'Akun berhasil dibuat. Selamat bergabung!');
        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::flush();
        Session::flash('success', 'Anda telah keluar dari sesi.');
        Response::redirect('/');
    }
}
