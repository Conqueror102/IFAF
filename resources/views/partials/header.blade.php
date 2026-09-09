    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="IFAF home">
            <img class="official-logo header-logo" src="{{ asset('images/brand/ifaf-primary.png') }}" alt="IFAF — International Food and Arts Festival" width="575" height="282">
            
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-menu-toggle>
            <span class="sr-only">Open navigation</span><i></i><i></i>
        </button>
        <nav class="site-nav" id="site-navigation" aria-label="Main navigation" data-navigation>
            <a href="{{ route('home') }}#about">About</a><a href="{{ route('home') }}#experience">Experience</a><a href="{{ route('gallery') }}" @if(request()->routeIs('gallery')) aria-current="page" @endif>Gallery</a><a href="{{ route('home') }}#contact">Contact</a>
            <a href="{{ route('booking.saved') }}">My tickets</a>
            <a class="ticket-link" href="{{ route('home') }}#tickets">Get tickets <span aria-hidden="true">↗</span></a>
        </nav>
    </header>
