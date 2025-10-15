<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Models\Event;
use App\Models\Participant;
use Exception;

class ParticipantManageController
{
    public function index(): void
    {
        $eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : null;
        if ($eventId !== null && $eventId <= 0) {
            $eventId = null;
        }

        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : null;
        if ($status === '') {
            $status = null;
        }

        $participants = Participant::list($eventId, $status, 50);
        $events = Event::all();
        $statuses = Participant::statuses();

        view('participants/manage/index', [
            'title' => 'Kelola Peserta',
            'participants' => $participants,
            'events' => $events,
            'statuses' => $statuses,
            'selectedEvent' => $eventId,
            'selectedStatus' => $status,
        ]);
    }

    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            Session::flash('error', 'Data peserta tidak valid.');
            Response::redirect('/manage/participants');
        }

        $participant = Participant::find($id);
        if ($participant === null) {
            Session::flash('error', 'Peserta tidak ditemukan.');
            Response::redirect('/manage/participants');
        }

        $statuses = Participant::statuses();

        view('participants/manage/show', [
            'title' => 'Detail Peserta',
            'participant' => $participant,
            'statuses' => $statuses,
        ]);
    }

    public function updateStatus(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim((string) $_POST['status']) : '';

        if ($id <= 0) {
            Session::flash('error', 'ID peserta tidak valid.');
            Response::redirect('/manage/participants');
        }

        $allowedStatuses = array_column(Participant::statuses(), 'code');
        if (!in_array($status, $allowedStatuses, true)) {
            Session::flash('error', 'Status tidak dikenali.');
            Response::redirect('/manage/participants/show?id=' . $id);
        }

        try {
            Participant::updateStatus($id, $status);
        } catch (Exception $exception) {
            Session::flash('error', 'Gagal memperbarui status peserta.');
            logger('Update participant status failed: ' . $exception->getMessage());
            Response::redirect('/manage/participants/show?id=' . $id);
        }

        Session::flash('success', 'Status peserta berhasil diperbarui.');
        Response::redirect('/manage/participants/show?id=' . $id);
    }

    public function destroy(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            Session::flash('error', 'ID peserta tidak valid.');
            Response::redirect('/manage/participants');
        }

        try {
            Participant::delete($id);
        } catch (Exception $exception) {
            Session::flash('error', 'Gagal menghapus data peserta.');
            logger('Delete participant failed: ' . $exception->getMessage());
            Response::redirect('/manage/participants');
        }

        Session::flash('success', 'Data peserta berhasil dihapus.');
        Response::redirect('/manage/participants');
    }
}
