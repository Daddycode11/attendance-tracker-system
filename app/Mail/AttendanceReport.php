<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AttendanceReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $employeeId,
        public Collection $records,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Attendance Report — ' . $this->employeeName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance-report',
        );
    }
}
