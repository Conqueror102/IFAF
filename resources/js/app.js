const toggle = document.querySelector('[data-menu-toggle]');
const siteHeader = document.querySelector('.site-header');
if (siteHeader) {
    const updateHeader = () => siteHeader.classList.toggle('is-scrolled', window.scrollY > 24);
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive:true });
    window.addEventListener('pageshow', updateHeader);
}
document.querySelectorAll('.ticket-option').forEach((option) => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.ticket-option').forEach((button) => button.setAttribute('aria-pressed', String(button === option)));
        const guests = Number(option.dataset.ticketGuests);
        document.querySelector('[data-pass-selection]').textContent = option.dataset.ticketName + ' · Admits ' + guests + (guests === 1 ? ' person' : ' people');
        document.querySelector('[data-pass-name]').textContent = option.dataset.ticketName;
        document.querySelector('[data-pass-price]').textContent = '₦' + Number(option.dataset.ticketPrice).toLocaleString('en-NG');
        const reserveLink = document.querySelector('[data-reserve-link]');
        if (reserveLink) reserveLink.href = '/reserve?option=' + [...document.querySelectorAll('.ticket-option')].indexOf(option);
    });
});
const navigation = document.querySelector('[data-navigation]');
const galleryDialog = document.querySelector('.gallery-dialog');
const album = document.querySelector('.gallery-grid');
const albumCards = [...document.querySelectorAll('[data-gallery-image]')];
let albumIndex = 0;
const selectAlbum = (index) => {
    albumIndex = (index + albumCards.length) % albumCards.length;
    albumCards.forEach((card, i) => {
        let offset = (i - albumIndex + albumCards.length) % albumCards.length;
        if (offset > Math.floor(albumCards.length / 2)) offset -= albumCards.length;
        card.style.setProperty('--offset', offset);
        card.style.setProperty('--depth', Math.abs(offset));
        card.style.zIndex = String(albumCards.length - Math.abs(offset));
        card.classList.toggle('is-active', offset === 0);
        card.tabIndex = offset === 0 ? 0 : -1;
    });
    const status = document.querySelector('[data-album-status]');
    if (status) status.textContent = String(albumIndex + 1).padStart(2, '0') + ' / ' + String(albumCards.length).padStart(2, '0');
};
if (album && albumCards.length) {
    selectAlbum(0);
    document.querySelector('[data-album-prev]').addEventListener('click', () => selectAlbum(albumIndex - 1));
    document.querySelector('[data-album-next]').addEventListener('click', () => selectAlbum(albumIndex + 1));
    album.addEventListener('keydown', (event) => {
        if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
        event.preventDefault();
        selectAlbum(albumIndex + (event.key === 'ArrowRight' ? 1 : -1));
        albumCards[albumIndex].focus();
    });
    let touchStart = null;
    album.addEventListener('touchstart', (event) => { touchStart = event.touches[0].clientX; }, { passive:true });
    album.addEventListener('touchend', (event) => {
        if (touchStart === null) return;
        const distance = event.changedTouches[0].clientX - touchStart;
        if (Math.abs(distance) > 45) selectAlbum(albumIndex + (distance < 0 ? 1 : -1));
        touchStart = null;
    }, { passive:true });
}
if (galleryDialog) {
    let galleryTrigger;
    document.querySelectorAll('[data-gallery-image]').forEach((card) => {
        card.addEventListener('click', () => {
            if (album && !card.classList.contains('is-active')) {
                selectAlbum(albumCards.indexOf(card));
                return;
            }
            galleryTrigger = card;
            const preview = galleryDialog.querySelector('img');
            preview.src = card.dataset.galleryImage;
            preview.alt = card.querySelector('img').alt;
            galleryDialog.querySelector('.gallery-dialog-caption').textContent = card.dataset.galleryCaption;
            galleryDialog.showModal();
        });
    });
    galleryDialog.querySelector('.gallery-close').addEventListener('click', () => galleryDialog.close());
    galleryDialog.addEventListener('click', (event) => {
        if (event.target !== galleryDialog) return;
        const bounds = galleryDialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) galleryDialog.close();
    });
    galleryDialog.addEventListener('close', () => galleryTrigger?.focus());
}
const flagToggle = document.querySelector('[data-flag-toggle]');
const collectionFilters = [...document.querySelectorAll('[data-collection-filter]')];
collectionFilters.forEach((filter) => {
    filter.addEventListener('click', () => {
        collectionFilters.forEach((button) => button.setAttribute('aria-pressed', String(button === filter)));
        let visible = 0;
        document.querySelectorAll('[data-collection-category]').forEach((card) => {
            card.hidden = filter.dataset.collectionFilter !== 'All' && card.dataset.collectionCategory !== filter.dataset.collectionFilter;
            if (!card.hidden) visible++;
        });
        document.querySelector('[data-collection-count]').textContent = `${visible} ${visible === 1 ? 'image' : 'images'}`;

    });
});
flagToggle?.addEventListener('click', () => {
    const paused = flagToggle.getAttribute('aria-pressed') !== 'true';
    flagToggle.setAttribute('aria-pressed', String(paused));
    flagToggle.setAttribute('aria-label', paused ? 'Resume sliding country flags' : 'Pause sliding country flags');
});
if (toggle && navigation) {
    const close = () => { navigation.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); };
    toggle.addEventListener('click', () => {
        const open = navigation.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
    });
    navigation.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
    document.addEventListener('keydown', (event) => event.key === 'Escape' && close());
}

const heroSlides = [...document.querySelectorAll('[data-hero-slide]')];
if (heroSlides.length) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let index = 0;
    let timer;
    const schedule = () => {
        clearInterval(timer);
        if (reducedMotion.matches || document.hidden) return;
        timer = setInterval(() => {
            index = (index + 1) % heroSlides.length;
            heroSlides.forEach((slide, i) => {
                slide.classList.toggle('is-active', i === index);
                slide.setAttribute('aria-hidden', String(i !== index));
            });
        }, 7000);
    };
    document.addEventListener('visibilitychange', schedule);
    reducedMotion.addEventListener('change', schedule);
    schedule();
}

const ticketCanvases = document.querySelectorAll('[data-ticket-qr]');
const ticketQrReady = ticketCanvases.length
    ? import('qrcode').then(({ default: QRCode }) => Promise.all(
        [...ticketCanvases].map((canvas) => QRCode.toCanvas(canvas, canvas.dataset.ticketQr, { width:220, margin:3, errorCorrectionLevel:'M' })),
    )).catch(() => {
        ticketCanvases.forEach((canvas) => { canvas.replaceWith(document.createTextNode('QR unavailable. Use the printed ticket code at entry.')); });
        throw new Error('QR code generation failed.');
    })
    : Promise.resolve();
document.querySelectorAll('[data-download-ticket]').forEach((button) => {
    button.addEventListener('click', async () => {
        const ticket = document.getElementById(button.dataset.downloadTicket);
        const status = button.parentElement.querySelector('.ticket-download-status');
        if (!ticket) return;
        button.disabled = true;
        button.textContent = 'Preparing image…';
        status.textContent = '';
        try {
            await ticketQrReady;
            await document.fonts.ready;
            await Promise.all([...ticket.querySelectorAll('img')].map((image) => image.complete
                ? Promise.resolve()
                : new Promise((resolve, reject) => {
                    image.addEventListener('load', resolve, { once:true });
                    image.addEventListener('error', reject, { once:true });
                })));
            const { toPng } = await import('html-to-image');
            const dataUrl = await toPng(ticket, {
                pixelRatio:2,
                cacheBust:true,
                backgroundColor:'#fff9e9',
                width:ticket.offsetWidth,
                height:ticket.scrollHeight,
                style:{ margin:'0' },
            });
            const download = document.createElement('a');
            download.download = button.dataset.ticketFilename;
            download.href = dataUrl;
            download.click();
            button.textContent = 'Downloaded!';
            status.textContent = 'Your PNG ticket has been saved.';
        } catch {
            button.textContent = 'Try download again';
            status.textContent = 'The image could not be created. Check your connection and try again.';
        } finally {
            button.disabled = false;
            setTimeout(() => { if (button.textContent === 'Downloaded!') button.textContent = 'Download PNG'; }, 2500);
        }
    });
});
const scannerButton = document.querySelector('[data-start-scanner]');
scannerButton?.addEventListener('click', async () => {
    scannerButton.disabled = true;
    const status = document.querySelector('[data-scanner-status]');
    status.textContent = 'Opening camera…';
    try {
        const { Html5QrcodeScanner } = await import('html5-qrcode');
        const scanner = new Html5QrcodeScanner('ticket-scanner', { fps:10, qrbox:{ width:230, height:230 } }, false);
        let scanned = false;
        scanner.render((code) => {
            if (scanned) return;
            scanned = true;
            const form = document.querySelector('[data-ticket-lookup]');
            form.elements.code.value = code;
            scanner.clear().catch(() => {}).finally(() => form.requestSubmit());
        }, () => {});
        status.textContent = 'Choose your camera, then point it at the ticket QR code.';
    } catch {
        status.textContent = 'Camera scanner could not start. Use the ticket code instead.';
        scannerButton.disabled = false;
    }
});

// Private booking shortcuts stay on this device; no customer directory is exposed.
const bookingStorageKey = 'ifaf.saved-bookings.v1';
const readBookings = () => {
    const value = JSON.parse(localStorage.getItem(bookingStorageKey) || '[]');
    return Array.isArray(value) ? value.filter((item) => item && /^[A-Za-z0-9]{48}$/.test(item.token) && /^IFAF-[A-Z0-9]{10}$/.test(item.reference)).slice(0, 30) : [];
};
const savedBooking = document.querySelector('[data-save-booking]');
if (savedBooking) {
    try {
        const item = { token:savedBooking.dataset.bookingToken, reference:savedBooking.dataset.bookingReference };
        localStorage.setItem(bookingStorageKey, JSON.stringify([item, ...readBookings().filter((entry) => entry.token !== item.token)].slice(0, 30)));
        const message = document.querySelector('[data-booking-save-status]');
        if (message) message.textContent = 'Saved in My tickets on this browser. Keep your private link as a backup.';
    } catch {
        const message = document.querySelector('[data-booking-save-status]');
        if (message) message.textContent = 'This browser could not save your shortcut. Bookmark or copy this private page link before leaving.';
    }
}
const savedList = document.querySelector('[data-saved-bookings]');
if (savedList) {
    const renderSavedBookings = () => {
        savedList.replaceChildren();
        let entries;
        try { entries = readBookings(); } catch { savedList.textContent = 'Browser storage is unavailable. Open your original private booking link to continue.'; return; }
        if (!entries.length) { savedList.textContent = 'No bookings saved on this browser yet. Your reservation will appear here after you create one.'; return; }
        entries.forEach((entry) => {
            const card = document.createElement('article'); card.className = 'saved-booking';
            const copy = document.createElement('div');
            const title = document.createElement('strong'); title.textContent = entry.reference;
            const status = document.createElement('small'); status.textContent = 'Open your booking to see its latest status.';
            copy.append(title, status);
            const actions = document.createElement('div'); actions.className = 'saved-booking-actions';
            const link = document.createElement('a'); link.className = 'booking-button'; link.href = '/booking/' + entry.token; link.textContent = 'Continue booking ↗';
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'booking-secondary'; remove.textContent = 'Remove shortcut';
            remove.addEventListener('click', () => {
                try {
                    localStorage.setItem(bookingStorageKey, JSON.stringify(readBookings().filter((item) => item.token !== entry.token)));
                    localStorage.removeItem('ifaf.draft.' + entry.token);
                    renderSavedBookings();
                } catch { status.textContent = 'Could not remove the shortcut. Try clearing this site’s browser data.'; }
            });
            actions.append(link, remove); card.append(copy, actions); savedList.append(card);
            fetch('/booking/' + entry.token + '/status', { headers:{ Accept:'application/json' }, cache:'no-store' })
                .then((response) => { if (!response.ok) throw new Error('Status unavailable'); return response.json(); })
                .then((data) => { status.textContent = data.label; if(data.status === 'confirmed') { link.textContent = 'View tickets ↗'; link.href += '/tickets'; } })
                .catch(() => { status.textContent = 'Status unavailable. Open the booking to try again.'; });
        });
    };
    renderSavedBookings();
    window.addEventListener('pageshow', (event) => { if (event.persisted) renderSavedBookings(); });
    window.addEventListener('storage', (event) => { if(event.key === bookingStorageKey) renderSavedBookings(); });
}
document.querySelector('[data-copy-reference]')?.addEventListener('click', async (event) => {
    const button = event.currentTarget;
    const status = document.querySelector('[data-copy-status]');
    try {
        await navigator.clipboard.writeText(button.dataset.copyReference);
        button.textContent = 'Copied!'; status.textContent = 'Paste it into your bank’s narration or transfer description.';
        setTimeout(() => { button.textContent = 'Copy reference'; }, 2500);
    } catch { status.textContent = 'Copy was unavailable. Select the reference above and copy it manually.'; }
});
const paymentDraft = document.querySelector('[data-payment-draft]');
if (paymentDraft) {
    const key = 'ifaf.draft.' + paymentDraft.dataset.paymentDraft;
    const fields = ['sender_name', 'customer_note'];
    try {
        const draft = JSON.parse(localStorage.getItem(key) || '{}');
        fields.forEach((name) => { if (!paymentDraft.elements[name].value && typeof draft[name] === 'string') paymentDraft.elements[name].value = draft[name]; });
    } catch { /* The form remains usable without browser storage. */ }
    paymentDraft.addEventListener('input', () => {
        try {
            localStorage.setItem(key, JSON.stringify(Object.fromEntries(fields.map((name) => [name, paymentDraft.elements[name].value]))));
            document.querySelector('[data-draft-status]').textContent = 'Typed details saved on this browser. After a reload, select your receipt file again. Submitted receipts stay saved.';
        } catch { document.querySelector('[data-draft-status]').textContent = 'Typed details could not be saved on this browser. Submit before leaving, or enter them again when you return.'; }
    });
}
const bookingWatch = document.querySelector('[data-booking-watch]');
if (bookingWatch) {
    try { localStorage.removeItem('ifaf.draft.' + bookingWatch.dataset.clearDraft); } catch {}
    let checking = false;
    const checkBooking = async () => {
        if(document.hidden || checking) return;
        checking = true;
        try {
            const response = await fetch(bookingWatch.dataset.bookingWatch, { headers:{ Accept:'application/json' }, cache:'no-store', signal:AbortSignal.timeout(10000) });
            if(!response.ok) throw new Error('Status unavailable');
            const data = await response.json();
            if(data.status !== bookingWatch.dataset.currentStatus) { window.location.reload(); return; }
            bookingWatch.textContent = 'Payment is still under review. We’re checking automatically—you don’t need to refresh.';
        } catch { bookingWatch.textContent = 'We couldn’t check for an update. We’ll retry automatically when connected.'; }
        finally { checking = false; }
    };
    setInterval(checkBooking, 15000);
    document.addEventListener('visibilitychange', checkBooking);
    window.addEventListener('online', checkBooking);
    window.addEventListener('pageshow', checkBooking);
    checkBooking();
}
