<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public Patient $patient;

    public string $portalUrl;

    public function __construct(Patient $patient, string $portalUrl)
    {
        $this->patient = $patient;
        $this->portalUrl = $portalUrl;
    }

    public function build(): self
    {
        return $this->subject('Accede a tu portal de paciente')
            ->markdown('emails.portal.welcome', [
                'patient' => $this->patient,
                'portalUrl' => $this->portalUrl,
            ]);
    }
}
