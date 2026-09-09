<h1>IFAF — {{ $booking->statusLabel() }}</h1>
<p>Hello {{ $booking->name }},</p>
<p>Your booking reference is <strong>{{ $booking->reference }}</strong>.</p>
<p>{{ $booking->ticket_name }} · {{ $booking->guests }} people · ₦{{ number_format($booking->amount) }}</p>
@if($booking->status === 'confirmed')<p>Your tickets are ready. Open your booking page to view and save them.</p>@elseif($booking->status === 'awaiting_payment')<p>Complete your bank transfer and submit your receipt before {{ $booking->expires_at->timezone('Africa/Lagos')->format('j M Y, g:i a') }} WAT.</p>@endif
@if($booking->review_note)<p>{{ $booking->review_note }}</p>@endif
<p><a href="{{ route('booking.show', $booking) }}">Open your private booking page</a></p>
<p>Keep this link private. It gives access to your booking and tickets.</p>
<p>3 October 2026 · Strong Tower Hall, Ikoyi, Lagos</p>
