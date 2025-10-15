<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(array $request, callable $next): mixed
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect('/login');
        }

        return $next();
    }
}
