<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FestivalSetting;
use App\Models\Ticket;
use App\Services\BookingMail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function login(): View
    {
        return view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt([...$data, 'is_admin' => true])) {
            throw ValidationException::withMessages(['email' => 'The administrator credentials are incorrect.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login');
    }

    public function index(Request $request): View
    {
        Booking::whereIn('status', ['awaiting_payment', 'needs_information'])->where('expires_at', '<=', now())->update(['status' => 'expired']);
        $query = Booking::query();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $search = '%'.mb_substr($request->string('search')->toString(), 0, 120).'%';
            $query->where(fn ($q) => $q->where('reference', 'like', $search)->orWhere('email', 'like', $search)->orWhere('name', 'like', $search));
        }

        return view('admin.index', ['bookings' => $query->latest()->paginate(20)->withQueryString(),
            'counts' => Booking::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'settings' => FestivalSetting::findOrFail(1)]);
    }

    public function show(Booking $booking): View
    {
        $booking->expireIfNeeded();
        $booking->load('events.user', 'tickets');

        return view('admin.booking', compact('booking'));
    }

    public function receipt(Booking $booking): StreamedResponse
    {
        abort_unless($booking->receipt_path && Storage::disk('local')->exists($booking->receipt_path), 404);

        return Storage::disk('local')->response($booking->receipt_path, 'receipt.'.pathinfo($booking->receipt_path, PATHINFO_EXTENSION), ['X-Content-Type-Options' => 'nosniff']);
    }

    public function review(Request $request, Booking $booking, BookingMail $mail): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:confirmed,needs_information,rejected', 'revision' => 'required|integer',
            'review_note' => 'required_unless:decision,confirmed|nullable|string|max:1500',
            'bank_transaction' => 'required_if:decision,confirmed|nullable|string|max:150',
            'verified' => 'accepted_if:decision,confirmed',
        ]);
        DB::transaction(function () use ($booking, $data, $request) {
            $locked = Booking::lockForUpdate()->findOrFail($booking->id);
            if ($locked->status !== 'under_review' || $locked->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['decision' => 'This booking changed since you opened it. Refresh before reviewing.']);
            }
            $transaction = $data['decision'] === 'confirmed' ? strtoupper(trim($data['bank_transaction'])) : null;
            if ($transaction && Booking::where('bank_transaction', $transaction)->exists()) {
                throw ValidationException::withMessages(['bank_transaction' => 'This bank transaction has already been used for another booking.']);
            }
            $locked->update(['status' => $data['decision'], 'review_note' => $data['review_note'] ?? null,
                'bank_transaction' => $transaction, 'revision' => $locked->revision + 1,
                'expires_at' => $data['decision'] === 'needs_information' ? now()->addHours(24) : $locked->expires_at]);
            if ($data['decision'] === 'confirmed') {
                for ($guest = 1; $guest <= $locked->guests; $guest++) {
                    $locked->tickets()->create(['code' => Str::random(48), 'guest_number' => $guest]);
                }
            }
            $locked->events()->create(['user_id' => $request->user()->id, 'action' => $locked->statusLabel(), 'note' => $data['review_note'] ?? 'Bank transaction verified.']);
        });
        $mail->send($booking->refresh());

        return back()->with('success', 'Review saved. The customer’s booking page has been updated.');
    }

    public function resend(Booking $booking, BookingMail $mail): RedirectResponse
    {
        $sent = $mail->send($booking);

        return back()->with('success', $sent ? 'Booking update sent.' : 'Email not sent. Demo bookings stay local; for live bookings check the delivery log.');
    }

    public function settings(): View
    {
        return view('admin.settings', ['settings' => FestivalSetting::findOrFail(1)]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:120', 'account_name' => 'required|string|max:150',
            'account_number' => 'required|digits:10', 'capacity' => 'required|integer|min:1|max:100000',
            'hold_hours' => 'required|integer|min:1|max:168', 'ticket_options' => 'required|array|size:3',
            'ticket_options.*.name' => 'required|string|max:60', 'ticket_options.*.price' => 'required|integer|min:1|max:10000000',
            'ticket_options.*.guests' => 'required|integer|min:1|max:20', 'demo' => 'required|boolean', 'sales_open' => 'required|boolean',
        ]);
        if (! $request->boolean('demo') && ($data['account_number'] === '0000000000' || str_contains(strtolower($data['bank_name'].' '.$data['account_name']), 'demo'))) {
            throw ValidationException::withMessages(['account_number' => 'Replace all demo bank details before enabling live payments.']);
        }
        DB::transaction(fn () => FestivalSetting::lockForUpdate()->findOrFail(1)->update($data));

        return back()->with('success', 'Settings saved. Existing bookings keep their original price and bank details.');
    }

    public function scan(): View
    {
        return view('admin.scan');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => 'required|string|max:300']);
        $code = basename(parse_url($data['code'], PHP_URL_PATH) ?: $data['code']);
        $ticket = Ticket::where('code', $code)->first();
        if (! $ticket) {
            return back()->withErrors(['code' => 'Ticket not found. Check the code or scan again.']);
        }

        return to_route('admin.ticket', $ticket);
    }

    public function ticket(Ticket $ticket): View
    {
        $ticket->load('booking');

        return view('admin.ticket', compact('ticket'));
    }

    public function checkin(Request $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket) {
            $locked = Ticket::lockForUpdate()->findOrFail($ticket->id);
            if ($locked->booking->status !== 'confirmed' || $locked->checked_in_at) {
                throw ValidationException::withMessages(['ticket' => 'Ticket is not valid for entry or has already been checked in.']);
            }
            if ($locked->booking->demo && ! FestivalSetting::findOrFail(1)->demo) {
                throw ValidationException::withMessages(['ticket' => 'Demo tickets cannot be used for live admission.']);
            }
            $locked->update(['checked_in_at' => now(), 'checked_in_by' => $request->user()->id]);
            $locked->booking->events()->create(['user_id' => $request->user()->id, 'action' => 'Guest '.$locked->guest_number.' checked in']);
        });

        return back()->with('success', 'Entry confirmed. Welcome to IFAF!');
    }
}
