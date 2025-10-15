<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CommitteeManageController;
use App\Controllers\DashboardController;
use App\Controllers\EventApplicationController;
use App\Controllers\EventController;
use App\Controllers\EventManageController;
use App\Controllers\ParticipantManageController;
use App\Controllers\ProfileController;
use App\Middleware\AuthMiddleware;

$router->get('/', [EventController::class, 'landing']);
$router->get('/events', [EventController::class, 'index']);
$router->get('/events/show', [EventController::class, 'show']);
$router->post('/events/apply-participant', [EventApplicationController::class, 'applyParticipant']);
$router->post('/events/apply-committee', [EventApplicationController::class, 'applyCommittee']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/profile', [ProfileController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/profile', [ProfileController::class, 'update'], [AuthMiddleware::class]);

$router->get('/manage/events', [EventManageController::class, 'index'], [AuthMiddleware::class]);
$router->get('/manage/events/create', [EventManageController::class, 'create'], [AuthMiddleware::class]);
$router->post('/manage/events', [EventManageController::class, 'store'], [AuthMiddleware::class]);
$router->get('/manage/events/edit', [EventManageController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/manage/events/update', [EventManageController::class, 'update'], [AuthMiddleware::class]);
$router->post('/manage/events/delete', [EventManageController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/manage/participants', [ParticipantManageController::class, 'index'], [AuthMiddleware::class]);
$router->get('/manage/participants/show', [ParticipantManageController::class, 'show'], [AuthMiddleware::class]);
$router->post('/manage/participants/update-status', [ParticipantManageController::class, 'updateStatus'], [AuthMiddleware::class]);
$router->post('/manage/participants/delete', [ParticipantManageController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/manage/committees', [CommitteeManageController::class, 'index'], [AuthMiddleware::class]);
$router->get('/manage/committees/show', [CommitteeManageController::class, 'show'], [AuthMiddleware::class]);
$router->post('/manage/committees/update-status', [CommitteeManageController::class, 'updateStatus'], [AuthMiddleware::class]);
$router->post('/manage/committees/delete', [CommitteeManageController::class, 'destroy'], [AuthMiddleware::class]);
