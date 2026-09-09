<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore the colours, flavours and cultures of the International Food and Arts Festival in Lagos.">
    <meta name="theme-color" content="#080807">
    <title>Gallery — IFAF</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gallery-page">
    <a class="skip-link" href="#main-content">Skip to content</a>
    @include('partials.header')
    <main id="main-content">
        <section class="collection-intro" aria-labelledby="collection-title">
            <a class="collection-back" href="{{ route('home') }}">← Back to the festival</a>
            <p class="gallery-kicker">The IFAF gallery</p>
            <h1 id="collection-title">A world of colour.<br><em>A feeling of home.</em></h1>
            <div class="collection-intro-bottom">
                <p>Flavours worth sharing. Traditions worth celebrating.<br>A little glimpse of the world we bring together.</p>
                <span class="collection-seal" aria-hidden="true">Different roots.<br>Shared joy. ✦</span>
            </div>
        </section>
        <section class="collection" aria-label="Festival gallery">
            <div class="collection-toolbar">
                <div class="collection-filters" role="group" aria-label="Filter gallery">
                    @foreach (['All', 'Togetherness', 'Food', 'Tradition', 'Art & music'] as $category)
                        <button type="button" data-collection-filter="{{ $category }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $category }}</button>
                    @endforeach
                </div>
                <p class="collection-count" data-collection-count aria-live="polite">{{ count(config('gallery.images')) }} images</p>
            </div>
            <p class="collection-note">A visual preview of IFAF · Illustrative artwork for now. Past-event photography coming soon.</p>
            <div class="collection-masonry" aria-label="Festival images">
                @foreach (config('gallery.images') as $photo)
                    <button class="collection-card" type="button" data-collection-category="{{ $photo['category'] }}" data-gallery-image="{{ asset('images/'.$photo['file']) }}" data-gallery-caption="{{ $photo['title'] }} · Illustrative artwork" aria-label="View image: {{ $photo['title'] }}">
                        <span class="collection-image"><img src="{{ asset('images/'.$photo['file']) }}" alt="{{ $photo['alt'] }}" loading="lazy"><span class="collection-enlarge" aria-hidden="true">↗</span></span>
                        <span class="collection-caption"><span><small>{{ $photo['category'] }}</small><strong>{{ $photo['title'] }}</strong></span><span class="collection-number" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span></span>
                    </button>
                @endforeach
            </div>
            <dialog class="gallery-dialog" aria-label="Gallery image preview">
                <button class="gallery-close" type="button" aria-label="Close image preview">×</button>
                <img alt="">
                <p class="gallery-dialog-caption"></p>
            </dialog>
        </section>
        <section class="collection-invitation">
            <p class="gallery-kicker">Make your own memories</p>
            <h2>The next picture<br>could have <span>you in it.</span></h2>
            <p>3 October 2026 · Strong Tower Hall, Ikoyi, Lagos</p>
            <a class="primary-button" href="{{ route('home') }}#tickets">Find your ticket <span aria-hidden="true">↗</span></a>
        </section>
    </main>
    @include('partials.footer')
</body>
</html>
