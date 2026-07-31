<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarningNotice extends Mailable
{
    use Queueable, SerializesModels;

    // 1. Declare the public property so the view can see it 🔑
    public $student;

    // 2. Accept the Student model when the mail is created 🛠️
    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    // 3. Set up the email subject 📨
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Status Warning: Action Required',
        );
    }

    // 4. Point to our Blade view layout 📄
    public function content(): Content
    {
        return new Content(
            view: 'emails.warning',
        );
    }
}