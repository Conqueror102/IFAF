<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FestivalSetting;
use App\Services\BookingMail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('booking.create', ['settings' => FestivalSetting::findOrFail(1)]);
    }

    public function store(Request $request, BookingMail $mail): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120', 'email' => 'required|email|max:190',
            'phone' => 'required|string|max:40', 'option' => 'required|integer|min:0|max:2',
            'consent' => 'accepted',
        ]);
        $booking = DB::transaction(function () use ($data) {
            $settings = FestivalSetting::lockForUpdate()->findOrFail(1);
            if (! $settings->sales_open) {
                throw ValidationException::withMessages(['option' => 'Reservations are currently closed.']);
            }
            if (! $settings->demo && (! $settings->bank_name || ! $settings->account_name || ! $settings->account_number || $settings->account_number === '0000000000')) {
                throw ValidationException::withMessages(['option' => 'Payment details are not ready yet. Please check back soon.']);
            }
            $option = $settings->ticket_options[$data['option']];
            Booking::whereIn('status', ['awaiting_payment', 'needs_information'])->where('expires_at', '<=', now())->update(['status' => 'expired']);
            $reserved = Booking::where('demo', $settings->demo)->whereIn('status', ['awaiting_payment', 'needs_information', 'under_review', 'confirmed'])->sum('guests');
            if ($reserved + $option['guests'] > $settings->capacity) {
                throw ValidationException::withMessages(['option' => 'There are not enough places for this ticket option.']);
            }
            $booking = Booking::create([
                'token' => Str::random(48), 'reference' => 'IFAF-'.strtoupper(Str::random(10)),
                'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'],
                'ticket_name' => $option['name'], 'guests' => $option['guests'], 'amount' => $option['price'],
                'bank_details' => $settings->only('bank_name', 'account_name', 'account_number'),
                'demo' => $settings->demo, 'expires_at' => now()->addHours($settings->hold_hours),
            ]);
            $booking->events()->create(['action' => 'Reservation created']);

            return $booking;
        });
        $mail->send($booking);

        return to_route('booking.show', $booking)->with('success', 'Your reservation is saved. Keep this private link to return to your booking.');
    }

    public function show(Booking $booking): View
    {
        $booking->expireIfNeeded();

        return view('booking.show', compact('booking'));
    }

    public function status(Booking $booking): JsonResponse
    {
        $booking->expireIfNeeded();

        return response()->json(['status' => $booking->status, 'label' => $booking->statusLabel()]);
    }

    public function payment(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'sender_name' => 'required|string|max:120', 'customer_note' => 'nullable|string|max:1500',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', 'acknowledgement' => 'accepted',
        ]);
        $path = $request->file('receipt')->store('receipts', 'local');
        abort_unless(is_string($path), 500, 'Receipt could not be saved. Please try again.');
        try {
            DB::transaction(function () use ($booking, $data, $path) {
                $locked = Booking::lockForUpdate()->findOrFail($booking->id);
                if (! in_array($locked->status, ['awaiting_payment', 'needs_information']) || $locked->expires_at->isPast()) {
                    throw ValidationException::withMessages(['receipt' => 'This booking is no longer accepting payment submissions. Refresh the page to see its status.']);
                }
                $locked->update(['sender_name' => $data['sender_name'], 'customer_note' => $data['customer_note'] ?? null,
                    'receipt_path' => $path, 'status' => 'under_review', 'revision' => $locked->revision + 1]);
                $locked->events()->create(['action' => 'Payment submitted', 'note' => 'Receipt stored: '.$path]);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return back()->with('success', 'Payment details received. An admin will verify the transfer before issuing tickets.');
    }

    public function tickets(Booking $booking): View
    {
        abort_unless($booking->status === 'confirmed', 403, 'Tickets are available after payment approval.');
        $booking->load('tickets');

        return view('booking.tickets', compact('booking'));
    }
}
