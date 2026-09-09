@extends('booking.layout')
@section('title', 'Your tickets')
@section('content')
<div class="ticket-page-toolbar"><a href="{{ route('booking.show', $booking) }}">← Back to booking</a></div>
<h1 class="booking-title">Your people.<br><em>Your tickets.</em></h1>
<p class="booking-lead">Download each festival pass as a PNG image. Each QR code admits one person and can be used once.</p>
@foreach($booking->tickets as $ticket)
<article class="issued-ticket" id="ticket-{{ $ticket->id }}" aria-label="Festival ticket for {{ $booking->name }}, guest {{ $ticket->guest_number }}">
    <div class="issued-ticket-main">
        <img class="issued-ticket-art" src="{{ asset('images/ifaf-issued-ticket-art.png') }}" alt="" width="1536" height="1024">
        <div class="issued-ticket-brand">
            <p class="issued-ticket-edition">LAGOS · 2026</p>
            <img class="official-logo issued-ticket-logo" src="{{ asset('images/brand/ifaf-primary.png') }}" alt="IFAF" width="575" height="282">
        </div>
    </div>
    <div class="issued-ticket-stub">
        <div class="issued-stub-heading"><span>FESTIVAL PASS</span><strong>ADMIT ONE</strong></div>
        <div class="issued-stub-body">
            <div class="issued-ticket-qr"><canvas data-ticket-qr="{{ route('admin.ticket', $ticket) }}" aria-label="Entry QR code for guest {{ $ticket->guest_number }}"></canvas><p>{{ $ticket->checked_in_at ? 'Already checked in' : 'Scan at the entrance' }}</p></div>
            <div class="issued-stub-details">
                <dl>
                    <div><dt>Guest</dt><dd>{{ $booking->name }}</dd></div>
                    <div class="issued-stub-pair"><div><dt>Ticket</dt><dd>{{ $booking->ticket_name }}</dd></div><div><dt>Pass</dt><dd>{{ sprintf('%02d', $ticket->guest_number) }} / {{ sprintf('%02d', $booking->guests) }}</dd></div></div>
                    <div><dt>Date</dt><dd>Saturday, 3 October 2026</dd></div>
                    <div><dt>Venue</dt><dd>Strong Tower Hall, Ikoyi<small>40 Alfred Rewane Road, Lagos</small></dd></div>
                </dl>
            </div>
        </div>
        <div class="issued-stub-reference"><span>{{ $booking->reference }}</span><small>Food included · One entry</small></div>
        <code class="issued-stub-code" aria-label="Ticket code">{{ $ticket->code }}</code>
        @if($booking->demo)<p class="ticket-demo-stamp">DEMO · NOT VALID FOR ENTRY</p>@endif
    </div>
</article>
<div class="ticket-download-row">
    <span>Guest {{ $ticket->guest_number }} of {{ $booking->guests }}</span>
    <button type="button" class="booking-button" data-download-ticket="ticket-{{ $ticket->id }}" data-ticket-filename="IFAF-{{ $booking->reference }}-guest-{{ $ticket->guest_number }}.png">Download PNG</button>
    <span class="ticket-download-status" role="status" aria-live="polite"></span>
</div>
@endforeach
@endsection
