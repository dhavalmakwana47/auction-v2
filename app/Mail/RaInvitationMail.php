<?php

namespace App\Mail;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RaInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Auction $auction, public User $ra) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mapping of Resolution Applicant (RA) for Corporate Debtor – Challenge Mechanism',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ra-invitation');
    }

    public function attachments(): array
    {
        return [];
    }
}
