<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Models\Committee;
use App\Models\Event;
use Exception;

class CommitteeManageController
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

        $committees = Committee::list($eventId, $status, 50);
        $events = Event::all();
        $statuses = Committee::statuses();

        view('committees/manage/index', [
            'title' => 'Kelola Panitia',
            'committees' => $committees,
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
            Session::flash('error', 'Data panitia tidak valid.');
            Response::redirect('/manage/committees');
        }

        $committee = Committee::find($id);
        if ($committee === null) {
            Session::flash('error', 'Panitia tidak ditemukan.');
            Response::redirect('/manage/committees');
        }

        $statuses = Committee::statuses();

        view('committees/manage/show', [
            'title' => 'Detail Panitia',
            'committee' => $committee,
            'statuses' => $statuses,
        ]);
    }

    public function updateStatus(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim((string) $_POST['status']) : '';

        if ($id <= 0) {
            Session::flash('error', 'ID panitia tidak valid.');
            Response::redirect('/manage/committees');
        }

        $allowedStatuses = array_column(Committee::statuses(), 'code');
        if (!in_array($status, $allowedStatuses, true)) {
            Session::flash('error', 'Status tidak dikenali.');
            Response::redirect('/manage/committees/show?id=' . $id);
        }

        try {
            Committee::updateStatus($id, $status);
        } catch (Exception $exception) {
            Session::flash('error', 'Gagal memperbarui status panitia.');
            logger('Update committee status failed: ' . $exception->getMessage());
            Response::redirect('/manage/committees/show?id=' . $id);
        }

        Session::flash('success', 'Status panitia berhasil diperbarui.');
        Response::redirect('/manage/committees/show?id=' . $id);
    }

    public function destroy(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            Session::flash('error', 'ID panitia tidak valid.');
            Response::redirect('/manage/committees');
        }

        try {
            Committee::delete($id);
        } catch (Exception $exception) {
            Session::flash('error', 'Gagal menghapus data panitia.');
            logger('Delete committee failed: ' . $exception->getMessage());
            Response::redirect('/manage/committees');
        }

        Session::flash('success', 'Data panitia berhasil dihapus.');
        Response::redirect('/manage/committees');
    }
}
