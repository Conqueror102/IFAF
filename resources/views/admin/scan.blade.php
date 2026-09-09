@extends('admin.layout')
@section('admin-content')
<h1 class="booking-title">Welcome <em>them in.</em></h1><p class="booking-lead">Scan a QR code or enter its ticket code. Review the guest before confirming admission.</p>
<div class="booking-columns"><section class="booking-panel"><button type="button" class="booking-button" data-start-scanner>Start camera scanner</button><div id="ticket-scanner"></div><p data-scanner-status role="status"></p></section><section class="booking-panel"><h2>Find a ticket</h2><form method="post" action="{{ route('admin.lookup') }}" data-ticket-lookup>@csrf<label>Ticket code or QR link<input name="code" required maxlength="300" autocomplete="off"></label><button type="submit" class="booking-button">Look up ticket</button></form><p class="booking-help">Camera access requires HTTPS or localhost. You can always paste the printed code instead.</p></section></div>
@endsection
