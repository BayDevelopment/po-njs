<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusKerjasamaMail extends Mailable
{

    use Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📋 Update Status Kerjasama - ' . $this->data['nama_perusahaan'],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.status-kerjasama',
            with: ['data' => $this->data],
        );
    }
}
