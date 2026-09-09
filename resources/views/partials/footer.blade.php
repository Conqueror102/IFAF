    <footer class="site-footer" id="contact" aria-label="Festival information">
        <div class="footer-artifacts" aria-hidden="true">
            @foreach (['musical-notes', 'paint-palette', 'rice-bowl', 'confetti', 'ticket', 'musical-notes', 'rice-bowl', 'paint-palette', 'confetti', 'ticket', 'paint-palette', 'musical-notes'] as $artifact)
                <img src="{{ asset('images/icons/'.$artifact.'.png') }}" alt="" width="96" height="96" loading="lazy" class="footer-artifact footer-artifact-{{ $loop->iteration }}">
            @endforeach
        </div>
        <div class="footer-invitation">
            <p>Your culture.<br><span>Your people. Your place.</span></p>
            <a class="footer-ticket-button" href="{{ route('home') }}#tickets">Choose your ticket <span aria-hidden="true">↗</span></a>
        </div>
        <div class="footer-information">
            <div class="footer-about"><img class="official-logo footer-logo" src="{{ asset('images/brand/ifaf-white.png') }}" alt="IFAF" width="575" height="282" loading="lazy">
                <p class="footer-label">International Food &amp; Arts Festival</p>
                <p>Bridging the divide.<br>Connecting cultures.</p>
                <p class="footer-organiser">Powered by<br><strong>The Elevation Church, Ikoyi</strong></p>
            </div>
            <nav class="footer-navigation" aria-label="Footer navigation">
                <p class="footer-label">Explore IFAF</p>
                <a href="{{ route('home') }}#about">About the festival</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="{{ route('home') }}#ticket-inclusions">What’s included</a>
                <a href="{{ route('home') }}#tickets">Tickets</a>
            </nav>
            <div class="footer-visit">
                <p class="footer-label">Meet us in Lagos</p>
                <p><time datetime="2026-10-03">Saturday, 3 October 2026</time></p>
                <address><strong>Strong Tower Hall</strong><br>40 Alfred Rewane Road<br>Ikoyi, Lagos</address>
                <a class="footer-directions" href="https://www.google.com/maps/search/?api=1&amp;query=Strong+Tower+Hall+40+Alfred+Rewane+Road+Ikoyi+Lagos" target="_blank" rel="noopener noreferrer">Get directions <span aria-hidden="true">↗</span><span class="sr-only"> (opens in a new tab)</span></a>
            </div>
        </div>
        <div class="footer-culture-banner">
            <img src="{{ asset('images/cultures-banner.jpg') }}" alt="People standing together in cultural attire from around the world" width="1376" height="768" loading="lazy">
        </div>
        <div class="footer-bottom">
            <span>© 2026 International Food and Arts Festival</span>
            <a href="#">Back to top ↑</a>
        </div>
    </footer>