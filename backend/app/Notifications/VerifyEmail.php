<?php
namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailNotification
{
    protected function verificationUrl($notifiable)
    {
        return 'http://localhost:8081/verifyUser?' . http_build_query(
                [
                    'verifyLink' => URL::temporarySignedRoute(
                        'verification.verify',
                        Carbon::now()->addMinutes(60),
                        ['id' => $notifiable->getKey()]
                    )
                ]
            );
    }
}
