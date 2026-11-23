let searchBtn = document.querySelector('#search-btn');
let searchBar = document.querySelector('.search-bar-container');
let formBtn = document.querySelector('#login-btn');
let loginForm = document.querySelector('.login-form-container');
let formClose = document.querySelector('#form-close');
let menu = document.querySelector('#menu-bar');
let navbar = document.querySelector('.navbar');
let videoBtn = document.querySelectorAll('.vid-btn');

// Every page shares this script, so each element may or may not exist.
window.onscroll = () =>{
    searchBtn?.classList.remove('fa-times');
    searchBar?.classList.remove('active');
    menu?.classList.remove('fa-times');
    navbar?.classList.remove('active');
    loginForm?.classList.remove('active');
}

menu?.addEventListener('click', () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
});

searchBtn?.addEventListener('click', () =>{
    searchBtn.classList.toggle('fa-times');
    searchBar.classList.toggle('active');
    if (searchBar.classList.contains('active')) {
        searchBar.querySelector('input')?.focus();
    }
});

formBtn?.addEventListener('click', () =>{
    loginForm?.classList.add('active');
});

formClose?.addEventListener('click', () =>{
    loginForm?.classList.remove('active');
});

videoBtn.forEach(btn =>{
    btn.addEventListener('click', ()=>{
        document.querySelector('.controls .active')?.classList.remove('active');
        btn.classList.add('active');
        let src = btn.getAttribute('data-src');
        document.querySelector('#video-slider').src = src;
    });
});

if (typeof Swiper !== 'undefined') {

    var swiper = new Swiper(".review-slider", {
        spaceBetween: 20,
        grabCursor:true,
        loop:true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            640: {
              slidesPerView: 1,
            },
            768: {
              slidesPerView: 2,
            },
            1024: {
              slidesPerView: 3,
            },
        },
    });

    var swiper = new Swiper(".brand-slider", {
        spaceBetween: 20,
        loop:true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            450: {
              slidesPerView: 2,
            },
            768: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 4,
            },
            1200: {
              slidesPerView: 5,
            },
          },
    });

}

// Flash messages: dismiss on click, fade out success messages after a while.
document.querySelectorAll('.flash').forEach(flash => {
    flash.querySelector('.flash-close')?.addEventListener('click', () => flash.remove());
    if (flash.classList.contains('flash-success')) {
        setTimeout(() => flash.remove(), 8000);
    }
});

// Forms that need a confirmation (cancel booking, delete destination).
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

// Booking forms: keep "leaving" after "arrival" and show a live total.
document.querySelectorAll('.booking-form').forEach(form => {
    const arrival = form.querySelector('[name="arrival"]');
    const departure = form.querySelector('[name="departure"]');
    const guests = form.querySelector('[name="guests"]');
    const destination = form.querySelector('[name="destination_id"]');
    const totalEl = form.querySelector('[data-booking-total]');

    const price = () => {
        if (!destination) return 0;
        const el = destination.tagName === 'SELECT' ? destination.selectedOptions[0] : destination;
        return parseFloat(el?.dataset.price || '0');
    };

    const update = () => {
        if (arrival?.value && departure) {
            const next = new Date(arrival.value + 'T00:00:00Z');
            next.setUTCDate(next.getUTCDate() + 1);
            departure.min = next.toISOString().slice(0, 10);
        }
        const n = parseInt(guests?.value || '0', 10);
        if (totalEl && price() > 0 && n > 0) {
            totalEl.textContent = 'total: $' + (price() * n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            totalEl.hidden = false;
        } else if (totalEl) {
            totalEl.hidden = true;
        }
    };

    [arrival, guests, destination].forEach(el => el?.addEventListener('input', update));
    destination?.addEventListener('change', update);
    update();
});
