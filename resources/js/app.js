const toggle = document.querySelector('[data-menu-toggle]');
document.querySelectorAll('.ticket-option').forEach((option) => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.ticket-option').forEach((button) => button.setAttribute('aria-pressed', String(button === option)));
        const guests = Number(option.dataset.ticketGuests);
        document.querySelector('[data-pass-selection]').textContent = option.dataset.ticketName + ' · Admits ' + guests + (guests === 1 ? ' person' : ' people');
        document.querySelector('[data-pass-name]').textContent = option.dataset.ticketName;
        document.querySelector('[data-pass-price]').textContent = '₦' + Number(option.dataset.ticketPrice).toLocaleString('en-NG');
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
            if (!card.classList.contains('is-active')) {
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
