@extends('booking.layout')
@section('title', 'Admin sign in')
@section('content')
<div class="admin-login booking-panel"><p class="booking-eyebrow">Organiser access</p><h1 class="booking-title">Welcome<br><em>back.</em></h1><form method="post" action="{{ route('admin.authenticate') }}">@csrf<label>Email<input name="email" type="email" required autocomplete="username" value="{{ old('email') }}"></label><label>Password<input name="password" type="password" required autocomplete="current-password"></label><button class="booking-button" type="submit">Sign in ↗</button></form></div>
@endsection
