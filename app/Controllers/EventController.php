<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Participant;

class EventController
{
    public function landing(): void
    {
        $events = Event::paginate(3);

        view('events/index', [
            'title' => 'Eventory',
            'events' => $events,
            'isLanding' => true,
        ]);
    }

    public function index(): void
    {
        $events = Event::all();

        view('events/index', [
            'title' => 'Daftar Event',
            'events' => $events,
            'isLanding' => false,
        ]);
    }

    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            Response::redirect('/events');
        }

        $event = Event::find($id);

        if ($event === null) {
            Response::redirect('/events');
        }

        $participantSummary = Participant::summaryForEvent($event['id']);
        $committeeSummary = Committee::summaryForEvent($event['id']);

        view('events/show', [
            'title' => 'Detail Event',
            'event' => $event,
            'isOpen' => Event::isRecruitmentOpen($event),
            'participantSummary' => $participantSummary,
            'committeeSummary' => $committeeSummary,
        ]);
    }
}
