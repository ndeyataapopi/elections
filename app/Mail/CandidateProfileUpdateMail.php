<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateProfileUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Candidate $candidate;
    public string $editUrl;
    public string $deadline;

    /**
     * Create a new message instance.
     */
    public function __construct(Candidate $candidate, string $editUrl, string $deadline)
    {
        $this->candidate = $candidate;
        $this->editUrl = $editUrl;
        $this->deadline = $deadline;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Update Your Candidate Profile',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate-profile-update',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
