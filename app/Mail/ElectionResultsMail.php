<?php

namespace App\Mail;

use App\Models\Election;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ElectionResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    public Election $election;
    public User $admin;
    public array $results;
    public string $approvalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Election $election, User $admin, array $results, string $approvalUrl)
    {
        $this->election = $election;
        $this->admin = $admin;
        $this->results = $results;
        $this->approvalUrl = $approvalUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Election Results Released - ' . $this->election->name . ' - Your Approval Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.election-results',
            with: [
                'election' => $this->election,
                'admin' => $this->admin,
                'results' => $this->results,
                'approvalUrl' => $this->approvalUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
