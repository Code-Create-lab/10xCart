<?php

namespace App\Mail;

use App\Support\Leads\LeadPresenter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Everything the email shows (available in the views as $summary) */
    public LeadPresenter $summary;

    public function __construct(public array $lead)
    {
        $this->summary = new LeadPresenter($lead);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->summary->subject(),
            // "Reply" in the admin's inbox goes straight to the visitor
            replyTo: ! empty($this->lead['email'])
                ? [new Address($this->lead['email'], $this->lead['name'])]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leads.new-lead',
            text: 'emails.leads.new-lead-text',
        );
    }
}
