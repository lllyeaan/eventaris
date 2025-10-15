<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Participant;

class DashboardController
{
    public function index(): void
    {
        $ownerId = (int) Session::get('user_id');

        $eventsSummary = Event::quotaSummary($ownerId);

        foreach ($eventsSummary as &$event) {
            $event['participants_remaining'] = max(
                0,
                (int) $event['participant_quota'] - (int) $event['participants_approved']
            );
            $event['committees_remaining'] = max(
                0,
                (int) $event['committee_quota'] - (int) $event['committees_approved']
            );
        }
        unset($event);

        view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => [
                'events' => Event::count($ownerId),
                'participants_pending' => Participant::countPending($ownerId),
                'committees_pending' => Committee::countPending($ownerId),
            ],
            'eventsSummary' => $eventsSummary,
        ]);
    }
}
