<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="International Food and Arts Festival — a celebration of global food, art and culture in Lagos on 3 October 2026.">
    <meta name="theme-color" content="#080807">
    <title>IFAF — International Food and Arts Festival</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <a class="brand" href="#" aria-label="IFAF home">
            <span class="brand-mark">IFAF</span>
            <span class="brand-name">International Food<br>and Arts Festival</span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-menu-toggle>
            <span class="sr-only">Open navigation</span><i></i><i></i>
        </button>
        <nav class="site-nav" id="site-navigation" aria-label="Main navigation" data-navigation>
            <a href="#about">About</a><a href="#experience">Experience</a><a href="#programme">Programme</a><a href="#contact">Contact</a>
            <a class="ticket-link" href="#tickets">Get tickets <span aria-hidden="true">↗</span></a>
        </nav>
    </header>
    <main id="main-content">
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-copy">
                @php
                    $flags = ['ng' => 'Nigeria', 'gh' => 'Ghana', 'za' => 'South Africa', 'ke' => 'Kenya', 'eg' => 'Egypt', 'gb' => 'United Kingdom', 'us' => 'United States', 'ca' => 'Canada', 'br' => 'Brazil', 'mx' => 'Mexico', 'fr' => 'France', 'de' => 'Germany', 'es' => 'Spain', 'it' => 'Italy', 'in' => 'India', 'cn' => 'China', 'jp' => 'Japan', 'kr' => 'South Korea', 'ae' => 'United Arab Emirates', 'au' => 'Australia'];
                @endphp
                <button class="flag-marquee" type="button" aria-label="Pause sliding country flags" aria-pressed="false" data-flag-toggle>
                    <span class="flag-track" aria-hidden="true">
                        @for ($copy = 0; $copy < 2; $copy++)
                            <span class="flag-group">
                                @foreach ($flags as $code => $country)
                                    <img src="/images/flags/{{ $code }}.png" alt="" width="40" height="28" title="{{ $country }}">
                                @endforeach
                            </span>
                        @endfor
                    </span>
                </button>
                <h1 id="hero-title">Bridging the divide.<br><em>Connecting cultures.</em></h1>
                <p class="hero-intro">A vibrant meeting of global food, art, music and tradition—celebrated together in the heart of Lagos.</p>
                <div class="hero-actions">
                    <a class="primary-button" href="#tickets">Reserve your place <span aria-hidden="true">↗</span></a>
                    <a class="text-link" href="#about">Discover IFAF <span aria-hidden="true">↓</span></a>
                </div>
            </div>
            <img class="hero-people" src="/images/ifaf-hero-cultural-frame-v2.png" alt="People from many countries wearing traditional cultural attire">
            <div class="event-strip" aria-label="Event information">
                <div class="event-detail">
                    <img src="https://img.icons8.com/dotty/96/D8A646/calendar.png" alt="">
                    <p><span>Date</span><strong>Saturday, 3 October 2026</strong></p>
                </div>
                <div class="event-detail">
                    <img src="https://img.icons8.com/dotty/96/D8A646/marker.png" alt="">
                    <p><span>Venue</span><strong>Strong Tower Hall, Ikoyi</strong></p>
                </div>
                <p class="event-note">One city. Many worlds.<br>One unforgettable day.</p>
            </div>
        </section>
        <section class="intro-section" id="about" style="background-image: url('{{ asset('images/ifaf-lagos-gold-background.png') }}')">
            <p class="section-kicker welcome-wordmark"><span>Welcome to</span><img src="{{ asset('images/ifaf-flags-wordmark-white.png') }}" alt="IFAF" width="1693" height="929" loading="lazy"></p>
            <h2>Where every culture<br>has a seat at the table.</h2>
            <p>Come for the flavours. Stay for the stories, performances and connections that make our world feel closer.</p>
        </section>
        <section class="festival-gallery" id="experience" aria-labelledby="gallery-title">
            <div class="gallery-heading">
                <div>
                    <p class="gallery-kicker">Flavours. Faces. Shared joy.</p>
                    <h2 id="gallery-title">A taste of <span>IFAF.</span></h2>
                </div>
                <p>A world of colour, a table of discoveries.<br>And a celebration that brings us closer.</p>
            </div>
            <div class="gallery-grid" tabindex="0" role="region" aria-label="Festival photo album. Use left and right arrow keys to browse.">
                <button class="gallery-card gallery-card-main" type="button" data-gallery-image="{{ asset('images/ifaf-festival-market.jpg') }}" data-gallery-caption="Different roots. One celebration." aria-label="Enlarge cultural festival scene">
                    <img src="{{ asset('images/ifaf-festival-market.jpg') }}" alt="Illustrative scene of people in cultural attire at an international market" loading="lazy" width="1376" height="768">
                    <span class="gallery-card-copy"><small>Togetherness</small><strong>Different roots.<br>One celebration.</strong></span>
                    <span class="gallery-expand" aria-hidden="true">↗</span>
                </button>
                <button class="gallery-card" type="button" data-gallery-image="{{ asset('images/ifaf-global-table.png') }}" data-gallery-caption="Good food travels far." aria-label="Enlarge global food scene">
                    <img src="{{ asset('images/ifaf-global-table.png') }}" alt="Generated scene of jollof rice, plantain, samosas, tacos and sushi on a shared table" loading="lazy" width="1448" height="1086">
                    <span class="gallery-card-copy"><small>Global flavours</small><strong>Good food travels far.</strong></span>
                    <span class="gallery-expand" aria-hidden="true">↗</span>
                </button>
                <button class="gallery-card" type="button" data-gallery-image="{{ asset('images/ifaf-cultural-stage.jpg') }}" data-gallery-caption="Wear your story." aria-label="Enlarge cultural attire scene">
                    <img src="{{ asset('images/ifaf-cultural-stage.jpg') }}" alt="Illustrative group portrait celebrating different traditional garments" loading="lazy" width="1376" height="768">
                    <span class="gallery-card-copy"><small>Living traditions</small><strong>Wear your story.</strong></span>
                    <span class="gallery-expand" aria-hidden="true">↗</span>
                </button>
            </div>
            <div class="album-controls">
                <button type="button" data-album-prev aria-label="Previous photo">←</button>
                <p data-album-status aria-live="polite" aria-atomic="true">01 / 03</p>
                <button type="button" data-album-next aria-label="Next photo">→</button>
            </div>
            <p class="album-hint">Turn through the celebration · Tap the front photo to expand</p>
            <p class="gallery-disclosure">Illustrative festival scenes. Past-event photography coming soon.</p>
            <dialog class="gallery-dialog" aria-label="Festival image preview">
                <button class="gallery-close" type="button" aria-label="Close image preview">×</button>
                <img alt="">
                <p class="gallery-dialog-caption"></p>
            </dialog>
        </section>
        <section class="culture-circle-section" aria-labelledby="culture-circle-title">
            <div class="culture-circle-art">
                <img src="{{ asset('images/ifaf-culture-circle.png') }}" alt="Illustrative circular collage of global food, painting, textiles, music, dance and friends sharing a meal" width="1672" height="941" loading="lazy">
            </div>
            <div class="culture-circle-copy">
                <p class="culture-circle-kicker">Different roots. Shared joy.</p>
                <h2 id="culture-circle-title">Many cultures.<br><span>One celebration.</span></h2>
                <p>Food opens the conversation. Art tells our stories. Music brings us closer. And tradition gives us something beautiful to share.</p>
                <p>Come with your culture.<br>Discover someone else’s.</p>
                <ul aria-label="What connects us">
                    <li>Food</li><li>Art</li><li>Music</li><li>Tradition</li>
                </ul>
            </div>
        </section>
        <section class="ticket-value" id="ticket-inclusions" aria-labelledby="ticket-value-title">
            <div class="ticket-value-heading">
                <p class="gallery-kicker">Your invitation to IFAF</p>
                <h2 id="ticket-value-title">Come hungry.<br><span>Leave connected.</span></h2>
                <p>Food is part of your ticket. Sharing a dish is up to you.</p>
            </div>
            <div class="ticket-value-grid">
                <article class="ticket-food-card">
                    <img src="{{ asset('images/ifaf-global-table.png') }}" alt="Illustrative spread of food from different cultures" width="1448" height="1086" loading="lazy">
                    <div class="ticket-food-copy">
                        <span class="ticket-badge">Included with admission</span>
                        <h3>A seat at the table.</h3>
                        <p>Your ticket includes food, so bring your appetite and discover flavours from different cultures.</p>
                    </div>
                </article>
                <article class="ticket-sharing-card">
                    <span class="ticket-badge">Completely optional</span>
                    <h3>Have a dish<br>you love to share?</h3>
                    <p>You’re welcome to bring food along. A family favourite, a taste of home, or something you enjoy sharing.</p>
                    <div class="ticket-sharing-note">
                        <strong>Your presence is enough.</strong>
                        <p>There’s no requirement to bring a dish. Food is included whether you bring something or simply come to enjoy it.</p>
                    </div>
                </article>
            </div>
            <p class="ticket-value-closing">Bring your appetite. Bring your people. <span>Come as you are.</span></p>
        </section>
        <section class="ticket-booking" id="tickets" aria-labelledby="booking-title">
            <div class="booking-intro">
                <p class="booking-eyebrow"><span aria-hidden="true">✦</span> Make it a date</p>
                <h2 id="booking-title">Your next<br>great memory<br><span>starts here.</span></h2>
                <p>A world of flavours. A meeting of cultures.<br>Your place in the celebration.</p>
                <div class="booking-date">
                    <span class="booking-date-day">03</span>
                    <span><strong>October 2026</strong><small>Saturday · Lagos, Nigeria</small></span>
                </div>
                <p class="booking-invitation">Come with friends.<br>Leave with a few more.</p>
            </div>
            <div class="festival-pass-wrap">
                <div class="ticket-options" role="group" aria-label="Choose a sample ticket option">
                    @foreach (config('ifaf.tickets') as $option)
                        <button type="button" class="ticket-option" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" data-ticket-name="{{ $option['name'] }}" data-ticket-guests="{{ $option['guests'] }}" data-ticket-price="{{ $option['price'] }}">
                            <strong>{{ $option['name'] }}</strong>
                            <span>₦{{ number_format($option['price']) }}</span>
                            <small>{{ $option['guests'] }} {{ $option['guests'] === 1 ? 'person' : 'people' }}</small>
                        </button>
                    @endforeach
                </div>
                <p class="ticket-draft-note">Sample ticket options and prices · Subject to confirmation</p>
                <div class="pass-top-label"><span>THE WORLD MEETS IN LAGOS</span><span aria-hidden="true">↘</span></div>
                <article class="festival-pass" aria-labelledby="pass-title">
                    <div class="pass-main" style="--ticket-pattern: url('{{ asset('images/ifaf-ticket-pattern.png') }}')">
                        <div class="pass-brand-row">
                            <span class="pass-wordmark">IFAF</span>
                            <span class="pass-edition">LAGOS<br><strong>2026</strong></span>
                        </div>
                        <p class="pass-event-name">International Food &amp; Arts Festival</p>
                        <p class="pass-selection" data-pass-selection>{{ config('ifaf.tickets.0.name') }} · Admits {{ config('ifaf.tickets.0.guests') }} person</p>
                        <h3 id="pass-title">One ticket.<br>A world together.</h3>
                        <div class="pass-flags" aria-hidden="true">
                            @foreach (['ng', 'gh', 'gb', 'in', 'jp', 'br', 'za', 'ca'] as $flag)
                                <img src="{{ asset('images/flags/'.$flag.'.png') }}" alt="" width="32" height="22" loading="lazy">
                            @endforeach
                        </div>
                        <dl class="pass-details">
                            <div><dt>When</dt><dd>Saturday, 3 October 2026</dd></div>
                            <div><dt>Where</dt><dd>Strong Tower Hall, Ikoyi</dd></div>
                        </dl>
                        <p class="pass-address">40 Alfred Rewane Road, Ikoyi, Lagos</p>
                        <div class="pass-food"><span aria-hidden="true">✓</span><span><strong>Food included</strong><small>Bringing a dish? That’s entirely optional.</small></span></div>
                    </div>
                    <div class="pass-perforation" aria-hidden="true"></div>
                    <div class="pass-stub">
                        <div class="pass-price-row" aria-live="polite" aria-atomic="true"><span>Total · <span data-pass-name>{{ config('ifaf.tickets.0.name') }}</span></span><strong data-pass-price>₦{{ number_format(config('ifaf.tickets.0.price')) }}</strong></div>
                        <button class="pass-checkout" type="button" disabled>Bank transfer coming soon <span aria-hidden="true">↗</span></button>
                        <p class="pass-sales-note">Bookings are not open yet. Verified bank details and payment instructions will appear here.</p>
                    </div>
                </article>
                <p class="pass-bottom-note">Different roots. Shared joy. <span>One IFAF.</span></p>
            </div>
        </section>
    </main>
    <footer class="site-footer" id="contact" aria-label="Festival information">
        <div class="footer-invitation">
            <p>Your culture.<br><span>Your people. Your place.</span></p>
            <a class="footer-ticket-button" href="#tickets">Choose your ticket <span aria-hidden="true">↗</span></a>
        </div>
        <div class="footer-information">
            <div class="footer-about">
                <p class="footer-label">International Food &amp; Arts Festival</p>
                <p>Bridging the divide.<br>Connecting cultures.</p>
                <p class="footer-organiser">Powered by<br><strong>The Elevation Church, Ikoyi</strong></p>
            </div>
            <nav class="footer-navigation" aria-label="Footer navigation">
                <p class="footer-label">Explore IFAF</p>
                <a href="#about">About the festival</a>
                <a href="#experience">A taste of IFAF</a>
                <a href="#ticket-inclusions">What’s included</a>
                <a href="#tickets">Tickets</a>
            </nav>
            <div class="footer-visit">
                <p class="footer-label">Meet us in Lagos</p>
                <p><time datetime="2026-10-03">Saturday, 3 October 2026</time></p>
                <address><strong>Strong Tower Hall</strong><br>40 Alfred Rewane Road<br>Ikoyi, Lagos</address>
                <a class="footer-directions" href="https://www.google.com/maps/search/?api=1&amp;query=Strong+Tower+Hall+40+Alfred+Rewane+Road+Ikoyi+Lagos" target="_blank" rel="noopener noreferrer">Get directions <span aria-hidden="true">↗</span><span class="sr-only"> (opens in a new tab)</span></a>
            </div>
        </div>
        <a class="footer-wordmark" href="#" aria-label="IFAF — back to top">IFAF<span aria-hidden="true">✦</span></a>
        <div class="footer-bottom">
            <span>© 2026 International Food and Arts Festival</span>
            <a href="https://icons8.com" target="_blank" rel="noopener noreferrer">Icons by Icons8</a>
            <a href="#">Back to top ↑</a>
        </div>
    </footer>
</body>
</html>
