<?php

namespace App\Mail;

use App\Models\Clinic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeClinic extends Mailable
{
    use Queueable, SerializesModels;

    public $clinic;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(Clinic $clinic)
    {
        $this->clinic = $clinic;

        $domain = $clinic->domains->first()->domain;
        $protocol = request()->secure() ? 'https://' : 'http://';
        $port = in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort();

        $this->url = $protocol . $domain . $port;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to DentalFlowSaaS!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-clinic',
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
