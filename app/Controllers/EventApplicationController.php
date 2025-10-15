<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Participant;
use Exception;

class EventApplicationController
{
    public function applyParticipant(): void
    {
        $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $event = $this->resolveEvent($eventId, '/events');

        $input = [
            'event_id' => $eventId,
            'participant_full_name' => trim((string) ($_POST['participant_full_name'] ?? '')),
            'participant_email' => trim((string) ($_POST['participant_email'] ?? '')),
            'participant_phone' => trim((string) ($_POST['participant_phone'] ?? '')),
            'participant_institution' => trim((string) ($_POST['participant_institution'] ?? '')),
            'participant_notes' => trim((string) ($_POST['participant_notes'] ?? '')),
        ];

        if (!Event::isRecruitmentOpen($event)) {
            Session::flash('error', 'Pendaftaran peserta untuk event ini sudah ditutup.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        $summary = Participant::summaryForEvent($eventId);
        if ($event['participant_quota'] > 0 && ($summary['pending'] + $summary['approved']) >= (int) $event['participant_quota']) {
            Session::flash('error', 'Kuota peserta sudah penuh.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'participant_full_name' => 'required|min:3|max:150',
            'participant_email' => 'required|email',
            'participant_phone' => 'required|min:8|max:20',
            'participant_institution' => 'required|min:3|max:150',
            'participant_notes' => 'max:500',
        ]);

        if (!$isValid) {
            Session::flash('participant_errors', $validator->errors());
            Session::flash('error', 'Gagal mengirim pendaftaran peserta.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        try {
            Participant::create([
                'event_id' => $eventId,
                'full_name' => $input['participant_full_name'],
                'email' => $input['participant_email'],
                'phone' => $input['participant_phone'],
                'institution' => $input['participant_institution'],
                'notes' => $input['participant_notes'],
                'status_code' => 'pending',
            ]);
        } catch (Exception $exception) {
            Session::flash('error', 'Terjadi kesalahan saat menyimpan pendaftaran.');
            logger('Participant apply failed: ' . $exception->getMessage());
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        Session::flash('success', 'Pendaftaran peserta berhasil dikirim. Tunggu konfirmasi panitia.');
        Response::redirect('/events/show?id=' . $eventId);
    }

    public function applyCommittee(): void
    {
        $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $event = $this->resolveEvent($eventId, '/events');

        $input = [
            'event_id' => $eventId,
            'committee_full_name' => trim((string) ($_POST['committee_full_name'] ?? '')),
            'committee_email' => trim((string) ($_POST['committee_email'] ?? '')),
            'committee_phone' => trim((string) ($_POST['committee_phone'] ?? '')),
            'committee_institution' => trim((string) ($_POST['committee_institution'] ?? '')),
            'committee_primary_division' => trim((string) ($_POST['committee_primary_division'] ?? '')),
            'committee_secondary_division' => trim((string) ($_POST['committee_secondary_division'] ?? '')),
            'committee_motivation' => trim((string) ($_POST['committee_motivation'] ?? '')),
        ];

        if (!Event::isRecruitmentOpen($event)) {
            Session::flash('error', 'Pendaftaran panitia untuk event ini sudah ditutup.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        $summary = Committee::summaryForEvent($eventId);
        if ($event['committee_quota'] > 0 && ($summary['pending'] + $summary['approved']) >= (int) $event['committee_quota']) {
            Session::flash('error', 'Kuota panitia sudah penuh.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'committee_full_name' => 'required|min:3|max:150',
            'committee_email' => 'required|email',
            'committee_phone' => 'required|min:8|max:20',
            'committee_institution' => 'required|min:3|max:150',
            'committee_primary_division' => 'required|min:3|max:150',
            'committee_secondary_division' => 'max:150',
            'committee_motivation' => 'required|min:10|max:500',
        ]);

        if (!$isValid) {
            Session::flash('committee_errors', $validator->errors());
            Session::flash('error', 'Gagal mengirim pendaftaran panitia.');
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        try {
            Committee::create([
                'event_id' => $eventId,
                'full_name' => $input['committee_full_name'],
                'email' => $input['committee_email'],
                'phone' => $input['committee_phone'],
                'institution' => $input['committee_institution'],
                'primary_division' => $input['committee_primary_division'],
                'secondary_division' => $input['committee_secondary_division'],
                'motivation' => $input['committee_motivation'],
                'status_code' => 'pending',
            ]);
        } catch (Exception $exception) {
            Session::flash('error', 'Terjadi kesalahan saat menyimpan aplikasi panitia.');
            logger('Committee apply failed: ' . $exception->getMessage());
            Session::flashInput($input);
            Response::redirect('/events/show?id=' . $eventId);
        }

        Session::flash('success', 'Aplikasi panitia berhasil dikirim. Tim akan segera meninjau.');
        Response::redirect('/events/show?id=' . $eventId);
    }

    private function resolveEvent(int $eventId, string $fallback): array
    {
        if ($eventId <= 0) {
            Response::redirect($fallback);
        }

        $event = Event::find($eventId);
        if ($event === null) {
            Session::flash('error', 'Event tidak ditemukan.');
            Response::redirect($fallback);
        }

        return $event;
    }
}
