const percentage = parseFloat(document.querySelector('.accordion_active_percentage').textContent);

document.documentElement.style.setProperty('--main-percent', percentage);

// Get the serlialized array of slide data from a hidden element on page, passed from the module
const slides = JSON.parse(
    document.querySelector('.slides_serialized').textContent
);

const CHEVRON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';

const slider = document.getElementById('slider');
let activeIndex = 0;

// Caption block colour per slide: black on the first, stepping lighter
// to the right. Lightness runs 0% → 55% evenly across the slides.
function captionColor(index) {
    const l = Math.round((index / (slides.length - 1)) * 55);
    return `hsl(228, 12%, ${l}%)`;
}

// Build the slides once, then just toggle the active class to animate.
function build() {
    slider.style.setProperty('--slide-count', slides.length);
    slider.innerHTML = slides.map((s, i) => `
        <div class="slide" data-index="${i}" style="background-image:url('${s.image}')">
            ${s.hotspots.map((h, k) => `
                <button class="hotspot" data-slide="${i}" data-spot="${k}" style="top:${h.top};left:${h.left}">${k + 1}</button>
            `).join('')}
            <div class="caption" style="--cap-bg:${captionColor(i)}">
                <div class="caption-text">
                    <h2>${s.title}</h2>
                    <div class="caption-body">${s.text || ''}</div>
                </div>
                <div class="chevron">${CHEVRON}</div>
            </div>
        </div>
    `).join('');
    setActive(activeIndex);
}

// Move the active class; the flex-grow transition does the rest.
function setActive(index) {
    activeIndex = index;
    slider.querySelectorAll('.slide').forEach((el, i) => {
        el.classList.toggle('is-active', i === index);
    });
}

// A click either opens a hotspot's modal (active slide) or navigates.
slider.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (hotspot) {
        // Hotspots are only clickable on the active slide (pointer-events),
        // so reaching here means: open the modal, don't re-navigate.
        e.stopPropagation();
        openModal(Number(hotspot.dataset.slide), Number(hotspot.dataset.spot));
        return;
    }
    const slide = e.target.closest('.slide');
    if (slide) setActive(Number(slide.dataset.index));
});

/* ------------------------------------------------------------------
   MODAL — one reusable overlay, filled from the hotspot's data
------------------------------------------------------------------ */
const overlay = document.getElementById('modalOverlay');
const modalEyebrow = document.getElementById('modalEyebrow');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');

function openModal(slideIndex, spotIndex) {
    const spot = slides[slideIndex].hotspots[spotIndex];
    modalEyebrow.textContent = slides[slideIndex].title;
    modalTitle.textContent = spot.title;
    // The hotspot text is rich text from the back end, so it has to be
    // injected as markup rather than as a plain string.
    modalBody.innerHTML = spot.body || '';
    overlay.classList.add('is-open');
}

function closeModal() {
    overlay.classList.remove('is-open');
}

// Close on the X, on backdrop click, and on Escape.
document.getElementById('modalClose').addEventListener('click', closeModal);
overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

build();
