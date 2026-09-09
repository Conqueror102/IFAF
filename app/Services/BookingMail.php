<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingMail
{
    public function send(Booking $booking): bool
    {
        // Demo bookings remain local; never send demo tickets to real addresses.
        if ($booking->demo) {
            return false;
        }
        if (in_array(config('mail.default'), ['log', 'array'])) {
            $booking->events()->create(['action' => 'Email not delivered', 'note' => 'Configure a real mail transport before sending booking updates.']);

            return false;
        }
        try {
            Mail::send('mail.booking', ['booking' => $booking], function ($message) use ($booking) {
                $message->to($booking->email, $booking->name)->subject('IFAF '.$booking->reference.' — '.$booking->statusLabel());
            });
            $booking->events()->create(['action' => 'Email sent', 'note' => 'Booking update sent.']);

            return true;
        } catch (\Throwable $e) {
            report($e);
            $booking->events()->create(['action' => 'Email failed', 'note' => 'Check mail delivery configuration and resend.']);

            return false;
        }
    }
}
