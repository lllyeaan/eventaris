<?php
use App\Core\Session;

$isAuthenticated = Session::has('user_id');
$userName = Session::get('user_name', 'Panitia');
?>
<header class="bg-white shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-semibold text-sky-600"><?= e(config('app.name', 'Eventory')); ?></a>
        <nav class="flex items-center gap-4 text-sm font-medium">
            <a href="/" class="<?= route_is('') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Home</a>
            <a href="/events" class="<?= route_is('events') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Events</a>
            <?php if ($isAuthenticated): ?>
                <a href="/dashboard" class="<?= route_is('dashboard') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Dashboard</a>
                <a href="/manage/events" class="<?= str_starts_with(trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/'), 'manage/events') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Events Manage</a>
                <a href="/manage/participants" class="<?= str_starts_with(trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/'), 'manage/participants') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Participants</a>
                <a href="/manage/committees" class="<?= str_starts_with(trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/'), 'manage/committees') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Committees</a>
                <a href="/profile" class="<?= route_is('profile') ? 'text-sky-600' : 'text-slate-600 hover:text-sky-600'; ?>">Profile</a>
            <?php endif; ?>
        </nav>
        <div class="flex items-center gap-3">
            <?php if ($isAuthenticated): ?>
                <span class="text-sm text-slate-500">Halo, <?= e($userName); ?></span>
                <form action="/logout" method="post">
                    <button type="submit" class="px-3 py-1.5 border border-sky-500 text-sky-600 rounded hover:bg-sky-50">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="text-sm text-slate-600 hover:text-sky-600">Login</a>
                <a href="/register" class="px-3 py-1.5 bg-sky-600 text-white rounded text-sm hover:bg-sky-700">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>
