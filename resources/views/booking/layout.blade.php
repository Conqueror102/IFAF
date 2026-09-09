<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"><meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Your booking') — IFAF</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head><body class="booking-app">
    <header class="booking-header"><a href="{{ route('home') }}" aria-label="IFAF home"><img class="official-logo header-logo" src="{{ asset('images/brand/ifaf-primary.png') }}" alt="IFAF — International Food and Arts Festival" width="575" height="282"></a><a href="{{ route('booking.saved') }}">My tickets</a><a href="{{ route('home') }}">← Festival home</a></header>
    @if(isset($booking) && ! auth()->user()?->is_admin)
        <div hidden data-save-booking data-booking-token="{{ $booking->token }}" data-booking-reference="{{ $booking->reference }}"></div>
    @endif
    <main class="booking-shell">
        @if(session('success'))<div class="booking-notice" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="booking-errors" role="alert"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main>
    <footer class="booking-foot">3 October 2026 · Strong Tower Hall, Ikoyi, Lagos<br>Different roots. Shared joy.</footer>
</body></html>
