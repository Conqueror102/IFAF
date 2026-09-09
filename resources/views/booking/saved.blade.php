@extends('booking.layout')
@section('title', 'My tickets')
@section('content')
<p class="booking-eyebrow">Pick up where you left off</p>
<h1 class="booking-title">Your next<br><em>great memory.</em></h1>
<p class="booking-lead">Continue your reservation, submit payment details or collect your approved tickets.</p>
<div data-saved-bookings aria-live="polite"></div>
<p class="booking-help">Bookings are saved on this browser. Clearing browser data or changing devices removes these shortcuts, so keep your private booking link too. On a shared device, remove the shortcut when you’re done.</p>
<noscript><p>Enable JavaScript to see bookings saved on this browser, or open your original private booking link.</p></noscript>
<a class="booking-button" href="{{ route('booking.create') }}">Reserve another ticket ↗</a>
@endsection
