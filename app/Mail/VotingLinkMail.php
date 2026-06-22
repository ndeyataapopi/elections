<?php

namespace App\Mail;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VotingLinkMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Voter $voter;
    public Election $election;
    public string $votingUrl;
    public string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Voter $voter, Election $election, string $votingUrl, string $token)
    {
        $this->voter = $voter;
        $this->election = $election;
        $this->votingUrl = $votingUrl;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Voting Link - ' . ($this->election->name ?? 'Upcoming Election'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.voting-link',
            with: [
                'voter' => $this->voter,
                'election' => $this->election,
                'votingUrl' => $this->votingUrl,
                'token' => $this->token,
            ],
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
