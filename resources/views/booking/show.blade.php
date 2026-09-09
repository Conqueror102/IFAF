@extends('booking.layout')
@section('title', $booking->reference)
@section('content')
<p class="booking-eyebrow">Your reservation · {{ $booking->reference }}</p>
<h1 class="booking-title">{{ $booking->statusLabel() }}<em>.</em></h1>
<p class="booking-lead">Hi {{ $booking->name }}. Return through <a href="{{ route('booking.saved') }}"><strong>My tickets</strong></a> on this browser whenever you’re ready.</p>
<p class="booking-help" data-booking-save-status>Keep your private booking link as a backup.</p>
@if($booking->demo)<div class="booking-demo"><strong>Demo only. Do not send money.</strong> Use any sample receipt to test the review flow. No emails are sent for demo bookings.</div>@endif
<div class="booking-columns">
    <section class="booking-panel">
        @if(in_array($booking->status, ['awaiting_payment', 'needs_information']))
            <p class="booking-eyebrow">02 / Bank transfer</p><h2>One step closer.</h2>
            <p>Transfer the exact amount using the details below.</p>
            <div class="transfer-reference">
                <strong>Your transfer description</strong>
                <div><code>{{ $booking->reference }}</code><button type="button" class="booking-button" data-copy-reference="{{ $booking->reference }}">Copy reference</button></div>
                <p>Paste this into your bank’s <strong>narration / transfer-description</strong> field so we can match your payment.</p>
                <span role="status" data-copy-status></span>
            </div>
            <dl class="booking-details"><div><dt>Amount</dt><dd class="booking-amount">₦{{ number_format($booking->amount) }}</dd></div>@foreach($booking->bank_details as $key => $value)<div><dt>{{ str_replace('_', ' ', ucfirst($key)) }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>
            <p class="booking-help">Submit by {{ $booking->expires_at->timezone('Africa/Lagos')->format('j M Y, g:i a') }} WAT. Once submitted, your reservation stays on hold while we review it.</p>
            @if($booking->review_note)<div class="booking-notice"><strong>Admin’s message</strong><br>{{ $booking->review_note }}</div>@endif
            <form method="post" action="{{ route('booking.payment', $booking) }}" enctype="multipart/form-data" data-payment-draft="{{ $booking->token }}">
                @csrf
                <h3>{{ $booking->status === 'needs_information' ? 'Update payment details' : 'I’ve made payment' }}</h3>
                <label>Sender’s account name<input name="sender_name" required maxlength="120" value="{{ old('sender_name', $booking->sender_name) }}"></label>
                <label>Transfer receipt<input type="file" name="receipt" accept="image/jpeg,image/png,application/pdf" required></label><p class="booking-help">JPG, PNG or PDF · Maximum 5 MB. Do not include your PIN, password or card details.</p>
                <p class="booking-help" data-draft-status>After a reload, you may need to select the receipt file again. Submitted receipts stay saved.</p>
                <label>Anything we should know? <small>(optional)</small><textarea name="customer_note" rows="3" maxlength="1500">{{ old('customer_note', $booking->customer_note) }}</textarea></label>
                <label class="booking-checkbox"><input type="checkbox" name="acknowledgement" value="1" required><span>{{ $booking->demo ? 'This is a test submission with no real transfer.' : 'I have made this transfer and understand the receipt will be checked against the bank transaction.' }}</span></label>
                <button class="booking-button" type="submit">Submit for verification ↗</button>
            </form>
        @elseif($booking->status === 'under_review')
            <p class="booking-eyebrow">03 / Payment verification</p><h2>We’ve got your details.</h2><p>Your receipt is awaiting admin review. Your place stays reserved while we check the transfer.</p>
            <p data-booking-watch="{{ route('booking.status', $booking) }}" data-current-status="{{ $booking->status }}" data-clear-draft="{{ $booking->token }}" role="status">We’ll check automatically. Your tickets will appear here after approval.</p>
            <p class="booking-help">You can leave this page and return through My tickets on this browser.</p>
            <noscript><a href="{{ route('booking.show', $booking) }}">Check status</a></noscript>
        @elseif($booking->status === 'confirmed')
            <p class="booking-eyebrow">04 / You’re on the list</p><h2>See you at IFAF.</h2><p>Your {{ $booking->guests }} {{ $booking->guests === 1 ? 'ticket is' : 'tickets are' }} ready. Each person has a separate QR code for entry.</p><a class="booking-button" href="{{ route('booking.tickets', $booking) }}">View &amp; save tickets ↗</a>
        @else
            <h2>{{ $booking->status === 'expired' ? 'Your reservation window ended.' : 'We couldn’t verify this payment.' }}</h2><p>{{ $booking->review_note ?: 'No payment submission was received before the deadline.' }}</p><p>If you already transferred money, keep your reference and proof of payment for the organiser. Do not pay again.</p><a class="booking-button" href="{{ route('booking.create') }}">Start a new reservation</a>
        @endif
    </section>
    <aside class="booking-panel booking-gold"><img class="official-logo pass-logo" src="{{ asset('images/brand/ifaf-black.png') }}" alt="IFAF" width="575" height="282"><h2>{{ $booking->ticket_name }}</h2><p>{{ $booking->guests }} {{ $booking->guests === 1 ? 'person' : 'people' }} · Food included</p><p class="booking-amount">₦{{ number_format($booking->amount) }}</p><hr><p>{{ $booking->name }}<br>{{ $booking->email }}</p><p>Saturday, 3 October 2026<br>Strong Tower Hall, Ikoyi<br>40 Alfred Rewane Road, Lagos</p><p class="booking-help">{{ $booking->reference }}<br>{{ $booking->demo ? 'Demo reservation · Not valid for admission' : 'Keep your booking link private.' }}</p></aside>
</div>
@endsection
