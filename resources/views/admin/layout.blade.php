@extends('booking.layout')
@section('title', 'Admin')
@section('content')
<nav class="admin-navigation" aria-label="Admin navigation"><a href="{{ route('admin.index') }}">Bookings</a><a href="{{ route('admin.scan') }}">Check in</a><a href="{{ route('admin.settings') }}">Settings</a><form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign out</button></form></nav>
@yield('admin-content')
@endsection
