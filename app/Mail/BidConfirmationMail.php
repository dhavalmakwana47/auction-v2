<?php

namespace App\Mail;

use App\Models\AuctionBid;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BidConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AuctionBid $bid) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->bid->auction->corporate_debtor_name . ' - CHALLENGE BID SUBMITTED',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.bid-confirmation');
    }

    public function attachments(): array
    {
        $bid = $this->bid;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.bid-confirmation-pdf', compact('bid'));

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'bid-confirmation.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
