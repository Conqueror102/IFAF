@extends('booking.layout')
@section('title', 'Reserve your ticket')
@section('content')
<p class="booking-eyebrow">01 / Reserve your place</p>
<h1 class="booking-title">Your place.<br><em>In a world together.</em></h1>
<p class="booking-lead">Choose your ticket. Make a bank transfer. We’ll verify your payment and issue your tickets.</p>
@if($settings->demo)<div class="booking-demo"><strong>Demo booking · No real payment</strong><br>These are sample prices and dummy bank details. Do not transfer money. Demo tickets are not valid for festival entry.</div>@endif
@if(! $settings->sales_open)
    <div class="booking-panel"><h2>Reservations are closed</h2><p>Please check back soon.</p></div>
@else
<form method="post" action="{{ route('booking.store') }}" class="booking-columns">
    @csrf
    <section class="booking-panel"><h2>Who’s joining us?</h2>
        <label>Your full name<input name="name" autocomplete="name" required maxlength="120" value="{{ old('name') }}"></label>
        <label>Email address<input type="email" name="email" autocomplete="email" required maxlength="190" value="{{ old('email') }}"></label>
        <label>Phone number<input type="tel" name="phone" autocomplete="tel" required maxlength="40" value="{{ old('phone') }}"></label>
        <p class="booking-help">No account needed. You’ll get a private booking link—keep it safe so you can return to submit payment or download tickets.</p>
        <label class="booking-checkbox"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>I understand my place is reserved for {{ $settings->hold_hours }} hours and tickets are issued only after payment verification. My details will be used to manage this booking.</span></label>
    </section>
    <section class="booking-panel booking-gold"><h2>Bring your people.</h2>
        <fieldset class="booking-options"><legend class="sr-only">Ticket type</legend>
        @foreach($settings->ticket_options as $option)
            <label class="booking-option"><input type="radio" name="option" value="{{ $loop->index }}" required @checked((string) old('option', request('option', 0)) === (string) $loop->index)><span><strong>{{ $option['name'] }}</strong><small>{{ $option['guests'] }} {{ $option['guests'] === 1 ? 'person' : 'people' }} · Food included</small></span><b>₦{{ number_format($option['price']) }}</b></label>
        @endforeach
        </fieldset>
        <p>Saturday, 3 October 2026<br>Strong Tower Hall, Ikoyi</p>
        <button class="booking-button" type="submit">{{ $settings->demo ? 'Create demo reservation' : 'Reserve your ticket' }} ↗</button>
        <p class="booking-help">You’ll see the transfer instructions on the next page. No payment is taken here.</p>
    </section>
</form>
@endif
@endsection
