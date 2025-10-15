<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Participant;
use DateTimeImmutable;
use Exception;

class EventManageController
{
    private const STATUS_OPTIONS = ['draft', 'open', 'closed'];

    public function index(): void
    {
        $events = Event::all();
        $participantSummary = Participant::summaryByEvent();
        $committeeSummary = Committee::summaryByEvent();

        foreach ($events as &$event) {
            $pid = (int) $event['id'];
            $participantData = $participantSummary[$pid] ?? ['approved' => 0, 'pending' => 0];
            $committeeData = $committeeSummary[$pid] ?? ['approved' => 0, 'pending' => 0];

            $event['participants_approved'] = $participantData['approved'] ?? 0;
            $event['participants_pending'] = $participantData['pending'] ?? 0;
            $event['committees_approved'] = $committeeData['approved'] ?? 0;
            $event['committees_pending'] = $committeeData['pending'] ?? 0;
        }
        unset($event);

        view('events/manage/index', [
            'title' => 'Kelola Event',
            'events' => $events,
        ]);
    }

    public function create(): void
    {
        $errors = flash('errors') ?? [];

        view('events/manage/create', [
            'title' => 'Tambah Event',
            'errors' => $errors,
            'statuses' => self::STATUS_OPTIONS,
        ]);
    }

    public function store(): void
    {
        $input = $this->sanitizeInput($_POST);
        $ownerId = (int) Session::get('user_id');
        if ($ownerId <= 0) {
            Session::flash('error', 'Sesi pengguna tidak valid. Silakan login kembali.');
            Response::redirect('/login');
        }

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'name' => 'required|min:3|max:150',
            'description' => 'required|min:10',
            'location' => 'required|min:3|max:150',
            'event_date' => 'required',
            'participant_quota' => 'required|numeric',
            'committee_quota' => 'required|numeric',
            'status' => 'required',
            'committee_divisions' => 'max:500',
        ]);

        $errors = $validator->errors();

        if (!in_array($input['status'], self::STATUS_OPTIONS, true)) {
            $errors['status'][] = 'Status tidak dikenal.';
        }

        if (!$this->validateQuota((string) $input['participant_quota'])) {
            $errors['participant_quota'][] = 'Kuota peserta harus angka minimal 1.';
        }

        if (!$this->validateQuota((string) $input['committee_quota'])) {
            $errors['committee_quota'][] = 'Kuota panitia harus angka minimal 1.';
        }

        $start = $this->parseDateTime($input['registration_start']);
        $end = $this->parseDateTime($input['registration_end']);
        if ($start !== null && $end !== null && $end < $start) {
            $errors['registration_end'][] = 'Periode akhir harus sesudah periode mulai.';
        }

        if (!empty($errors)) {
            logger('Create event validation errors: ' . json_encode($errors, JSON_THROW_ON_ERROR));
            Session::flash('errors', $errors);
            Session::flash('error_messages', $this->formatErrorMessages($errors));
            Session::flashInput($input);
            Response::redirect('/manage/events/create');
        }

        try {
            Event::create([
                'owner_id' => $ownerId,
                'name' => $input['name'],
                'description' => $input['description'],
                'location' => $input['location'],
                'event_date' => $this->formatDate($input['event_date']),
                'participant_quota' => (int) $input['participant_quota'],
                'committee_quota' => (int) $input['committee_quota'],
                'registration_start' => $this->formatDateTimeForDb($start),
                'registration_end' => $this->formatDateTimeForDb($end),
                'status' => $input['status'],
                'committee_divisions' => $this->normalizeDivisions($input['committee_divisions']),
            ]);
        } catch (Exception $exception) {
            Session::flash('error', 'Terjadi kesalahan saat menyimpan event.');
            logger('Create event failed: ' . $exception->getMessage());
            Session::flashInput($input);
            Response::redirect('/manage/events/create');
        }

        Session::flash('success', 'Event berhasil ditambahkan.');
        Response::redirect('/manage/events');
    }

    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            Response::redirect('/manage/events');
        }

        $event = Event::find($id);
        if ($event === null) {
            Session::flash('error', 'Event tidak ditemukan.');
            Response::redirect('/manage/events');
        }

        $errors = flash('errors') ?? [];

        view('events/manage/edit', [
            'title' => 'Ubah Event',
            'event' => $event,
            'errors' => $errors,
            'statuses' => self::STATUS_OPTIONS,
        ]);
    }

    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            Session::flash('error', 'ID event tidak valid.');
            Response::redirect('/manage/events');
        }

        $event = Event::find($id);
        if ($event === null) {
            Session::flash('error', 'Event tidak ditemukan.');
            Response::redirect('/manage/events');
        }

        $input = $this->sanitizeInput($_POST);

        $validator = new Validator();
        $isValid = $validator->validate($input, [
            'name' => 'required|min:3|max:150',
            'description' => 'required|min:10',
            'location' => 'required|min:3|max:150',
            'event_date' => 'required',
            'participant_quota' => 'required|numeric',
            'committee_quota' => 'required|numeric',
            'status' => 'required',
            'committee_divisions' => 'max:500',
        ]);

        $errors = $validator->errors();

        if (!in_array($input['status'], self::STATUS_OPTIONS, true)) {
            $errors['status'][] = 'Status tidak dikenal.';
        }

        if (!$this->validateQuota((string) $input['participant_quota'])) {
            $errors['participant_quota'][] = 'Kuota peserta harus angka minimal 1.';
        }

        if (!$this->validateQuota((string) $input['committee_quota'])) {
            $errors['committee_quota'][] = 'Kuota panitia harus angka minimal 1.';
        }

        $start = $this->parseDateTime($input['registration_start']);
        $end = $this->parseDateTime($input['registration_end']);
        if ($start !== null && $end !== null && $end < $start) {
            $errors['registration_end'][] = 'Periode akhir harus sesudah periode mulai.';
        }

        if (!empty($errors)) {
            logger('Update event validation errors: ' . json_encode($errors, JSON_THROW_ON_ERROR));
            Session::flash('errors', $errors);
            Session::flash('error_messages', $this->formatErrorMessages($errors));
            Session::flashInput($input);
            Response::redirect('/manage/events/edit?id=' . $id);
        }

        try {
            Event::update($id, [
                'name' => $input['name'],
                'description' => $input['description'],
                'location' => $input['location'],
                'event_date' => $this->formatDate($input['event_date']),
                'participant_quota' => (int) $input['participant_quota'],
                'committee_quota' => (int) $input['committee_quota'],
                'registration_start' => $this->formatDateTimeForDb($start),
                'registration_end' => $this->formatDateTimeForDb($end),
                'status' => $input['status'],
                'committee_divisions' => $this->normalizeDivisions($input['committee_divisions']),
            ]);
        } catch (Exception $exception) {
            Session::flash('error', 'Terjadi kesalahan saat memperbarui event.');
            logger('Update event failed: ' . $exception->getMessage());
            Session::flashInput($input);
            Response::redirect('/manage/events/edit?id=' . $id);
        }

        Session::flash('success', 'Event berhasil diperbarui.');
        Response::redirect('/manage/events');
    }

    public function destroy(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            Session::flash('error', 'ID event tidak valid.');
            Response::redirect('/manage/events');
        }

        try {
            Event::delete($id);
        } catch (Exception $exception) {
            Session::flash('error', 'Gagal menghapus event. Pastikan tidak ada data terkait.');
            logger('Delete event failed: ' . $exception->getMessage());
            Response::redirect('/manage/events');
        }

        Session::flash('success', 'Event berhasil dihapus.');
        Response::redirect('/manage/events');
    }

    private function sanitizeInput(array $input): array
    {
        return [
            'id' => isset($input['id']) ? (int) $input['id'] : null,
            'name' => trim((string) ($input['name'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'location' => trim((string) ($input['location'] ?? '')),
            'event_date' => trim((string) ($input['event_date'] ?? '')),
            'participant_quota' => trim((string) ($input['participant_quota'] ?? '')),
            'committee_quota' => trim((string) ($input['committee_quota'] ?? '')),
            'registration_start' => trim((string) ($input['registration_start'] ?? '')),
            'registration_end' => trim((string) ($input['registration_end'] ?? '')),
            'status' => trim((string) ($input['status'] ?? 'draft')),
            'committee_divisions' => trim((string) ($input['committee_divisions'] ?? '')),
        ];
    }

    private function parseDateTime(string $value): ?DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $value = str_replace(' ', 'T', $value);

        try {
            $format = str_contains($value, 'T') ? 'Y-m-d\TH:i' : 'Y-m-d H:i:s';
            $date = DateTimeImmutable::createFromFormat($format, $value);
            return $date ?: null;
        } catch (Exception) {
            return null;
        }
    }

    private function formatDateTimeForDb(?DateTimeImmutable $dateTime): ?string
    {
        return $dateTime?->format('Y-m-d H:i:s');
    }

    private function formatDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        try {
            $date = new DateTimeImmutable($value);
            return $date->format('Y-m-d');
        } catch (Exception) {
            return null;
        }
    }

    private function validateQuota(string $value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        return (int) $value >= 1;
    }

    private function formatErrorMessages(array $errors): array
    {
        $messages = [];
        $labels = [
            'name' => 'Nama Event',
            'description' => 'Deskripsi / Rules',
            'location' => 'Lokasi',
            'event_date' => 'Tanggal Event',
            'participant_quota' => 'Kuota Peserta',
            'committee_quota' => 'Kuota Panitia',
            'registration_start' => 'Mulai Pendaftaran',
            'registration_end' => 'Akhir Pendaftaran',
            'status' => 'Status Open Recruitment',
            'committee_divisions' => 'Daftar Divisi Panitia',
        ];
        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $message) {
                $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $messages[] = $label . ': ' . $message;
            }
        }

        return $messages;
    }

    private function normalizeDivisions(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $parts = preg_split('/[\r\n,]+/', $value) ?: [];
        $clean = [];
        foreach ($parts as $part) {
            $trimmed = trim($part);
            if ($trimmed !== '') {
                $clean[] = $trimmed;
            }
        }

        if (empty($clean)) {
            return null;
        }

        $clean = array_values(array_unique($clean));

        return implode(PHP_EOL, $clean);
    }
}
