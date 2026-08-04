/* ------------------------------------------------------------------
   Bright Cloud Studio's Modal Gallery — Comparison style

   Builds a multi-phase comparison slider from the slide data the module
   emits into the page. Order matters: the first slide is the base layer
   (always fully visible on the right); each later slide stacks on top of
   it and is revealed from the left as the handles are dragged.

   Every gallery on the page is initialised independently, so several
   comparison modules can live in the same layout.
------------------------------------------------------------------ */
(function () {
    'use strict';

    var HANDLE_GAP = 2;          // Minimum % gap kept between adjacent handles
    var HOTSPOT_PREFIX = 'P';    // Prefix on the hotspot badges: P1, P2, …
    var KEY_STEP = 1;            // % a handle moves per arrow key (× 10 with Shift)
    var LABEL_MIN_WIDTH = 110;   // px of visible strip needed before a title shows

    // Per-layer hotspot colours; cycles if there are more slides than colours
    var COLORS = ['#e74c3c', '#f1c40f', '#2ecc71', '#3498db', '#9b59b6', '#e67e22'];

    var initGallery = function (root) {
        var slider = root.querySelector('.comparison_slider');
        var dataEl = root.querySelector('.comparison_data');

        if (!slider || !dataEl) {
            return;
        }

        var slides;

        try {
            slides = JSON.parse(dataEl.textContent);
        } catch (e) {
            return;
        }

        if (!Array.isArray(slides) || slides.length === 0) {
            return;
        }

        var N = slides.length;

        // Native canvas size: hotspot coordinates are locked to it and the
        // slider keeps its shape at any width.
        var canvasW = parseInt(root.getAttribute('data-canvas-width'), 10) || 800;
        var canvasH = parseInt(root.getAttribute('data-canvas-height'), 10) || 500;

        slider.style.aspectRatio = canvasW + ' / ' + canvasH;

        // Runtime element references, filled during build
        var slideEls = [];
        var handleEls = [];

        // Divider positions as percentages, one per gap between slides
        // (N-1 total), spread evenly across the canvas to start.
        var pcts = [];
        var i;

        for (i = 1; i < N; i++) {
            pcts.push((i * 100) / N);
        }

        /* ----------------------------------------------------------
           MODAL — one reusable overlay per gallery, filled on open
        ---------------------------------------------------------- */
        var overlay = root.querySelector('.comparison_modal_overlay');
        var eyebrowEl = root.querySelector('.comparison_modal_eyebrow');
        var titleEl = root.querySelector('.comparison_modal_title');
        var bodyEl = root.querySelector('.comparison_modal_body');
        var closeEl = root.querySelector('.comparison_modal_close');

        var closeModal = function () {
            if (overlay) {
                overlay.classList.remove('is-open');
            }
        };

        var openModal = function (slideIndex, spotIndex) {
            if (!overlay) {
                return;
            }

            var spot = slides[slideIndex].hotspots[spotIndex];

            if (eyebrowEl) { eyebrowEl.textContent = slides[slideIndex].title || ''; }
            if (titleEl)   { titleEl.textContent = spot.title || ''; }
            if (bodyEl)    { bodyEl.textContent = spot.body || ''; }

            overlay.classList.add('is-open');

            if (closeEl) {
                closeEl.focus();
            }
        };

        if (overlay) {
            // Close on the X, on a backdrop click, and on Escape
            if (closeEl) {
                closeEl.addEventListener('click', closeModal);
            }

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        }

        /* ----------------------------------------------------------
           LAYOUT — position the handles and clip each layer at its
           divider. The topmost slide (highest index) uses the leftmost
           divider, so the percentages are read in reverse.
        ---------------------------------------------------------- */
        var updateLayers = function () {
            var widths = [];
            var sliderWidth = slider.clientWidth;
            var j;

            // Base layer always spans the full width
            widths[0] = 100;

            for (j = 1; j < N; j++) {
                widths[j] = pcts[N - 1 - j];
            }

            slideEls.forEach(function (layer, index) {
                layer.style.width = widths[index] + '%';

                var label = layer.querySelector('.comparison_label');

                if (!label) {
                    return;
                }

                // Only the sliver between this layer's divider and the one
                // above it is on show; keep the label inside it.
                var covered = (index === N - 1) ? 0 : widths[index + 1];
                var visible = widths[index] - covered;
                var ratio = widths[index] > 0 ? (visible / widths[index] * 100) : 0;

                label.style.maxWidth = 'calc(' + ratio + '% - 2 * var(--cg-label-inset))';
                label.style.display = (sliderWidth * visible / 100) < LABEL_MIN_WIDTH ? 'none' : '';
            });

            handleEls.forEach(function (handle, index) {
                handle.style.left = pcts[index] + '%';
                handle.setAttribute('aria-valuenow', Math.round(pcts[index]));
            });
        };

        // Keep a minimum gap to the previous and next dividers (or the edges)
        var clampPct = function (index, value) {
            var lower = index === 0 ? 0 : pcts[index - 1] + HANDLE_GAP;
            var upper = index === N - 2 ? 100 : pcts[index + 1] - HANDLE_GAP;

            return Math.min(Math.max(value, lower), upper);
        };

        /* ----------------------------------------------------------
           DRAG — move a handle, clamped between its neighbours
        ---------------------------------------------------------- */
        var setupHandle = function (handle, index) {
            var onMove = function (e) {
                var rect = slider.getBoundingClientRect();

                if (!rect.width) {
                    return;
                }

                var offset = Math.min(Math.max(e.clientX - rect.left, 0), rect.width);

                pcts[index] = clampPct(index, (offset / rect.width) * 100);
                updateLayers();
            };

            var stopDrag = function (e) {
                handle.classList.remove('is-dragging');
                handle.removeEventListener('pointermove', onMove);

                if (handle.hasPointerCapture && handle.hasPointerCapture(e.pointerId)) {
                    handle.releasePointerCapture(e.pointerId);
                }
            };

            handle.addEventListener('pointerdown', function (e) {
                e.preventDefault();
                handle.classList.add('is-dragging');
                handle.setPointerCapture(e.pointerId);
                handle.addEventListener('pointermove', onMove);
            });

            handle.addEventListener('pointerup', stopDrag);
            handle.addEventListener('pointercancel', stopDrag);

            // Keyboard equivalent of dragging
            handle.addEventListener('keydown', function (e) {
                var step = e.shiftKey ? KEY_STEP * 10 : KEY_STEP;

                if (e.key === 'ArrowLeft') {
                    pcts[index] = clampPct(index, pcts[index] - step);
                } else if (e.key === 'ArrowRight') {
                    pcts[index] = clampPct(index, pcts[index] + step);
                } else {
                    return;
                }

                e.preventDefault();
                updateLayers();
            });
        };

        /* ----------------------------------------------------------
           BUILD — layers, hotspots and handles from the slide data
        ---------------------------------------------------------- */
        slides.forEach(function (slide, j) {
            // Clipping window. Higher index sits on top (narrower, left).
            var layer = document.createElement('div');
            layer.className = 'comparison_slide';
            layer.style.zIndex = j + 1;

            // Coordinate lock keeps the image and hotspots at native size
            var lock = document.createElement('div');
            lock.className = 'comparison_lock';
            lock.style.width = canvasW + 'px';   // replaced by syncSize()

            var img = document.createElement('img');
            img.src = slide.image;
            img.alt = slide.alt || slide.title || '';
            lock.appendChild(img);

            (slide.hotspots || []).forEach(function (spot, k) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'comparison_hotspot';
                btn.style.top = spot.top;
                btn.style.left = spot.left;
                btn.style.backgroundColor = COLORS[j % COLORS.length];
                btn.textContent = HOTSPOT_PREFIX + (j + 1);
                btn.setAttribute('aria-label', spot.title || '');

                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    openModal(j, k);
                });

                lock.appendChild(btn);
            });

            layer.appendChild(lock);

            // Caption title, pinned to the right edge of this layer's strip
            if (slide.title) {
                var label = document.createElement('span');
                label.className = 'comparison_label';
                label.textContent = slide.title;
                layer.appendChild(label);
            }

            slider.appendChild(layer);
            slideEls.push(layer);
        });

        // One handle per divider between slides
        for (i = 0; i < N - 1; i++) {
            var handle = document.createElement('div');
            handle.className = 'comparison_handle';
            handle.style.zIndex = 50 + i;
            handle.tabIndex = 0;
            handle.setAttribute('role', 'slider');
            handle.setAttribute('aria-valuemin', '0');
            handle.setAttribute('aria-valuemax', '100');

            // Label the two phases this handle sits between
            var leftPhase = N - i;          // phase revealed to the left
            var rightPhase = N - i - 1;     // phase revealed to the right

            var button = document.createElement('span');
            button.className = 'comparison_handle_button';
            button.textContent = leftPhase + '–' + rightPhase;
            handle.appendChild(button);

            handle.setAttribute('aria-label', 'Comparison divider ' + leftPhase + '/' + rightPhase);

            setupHandle(handle, i);
            slider.appendChild(handle);
            handleEls.push(handle);
        }

        /* ----------------------------------------------------------
           RESPONSIVE SIZING
           The slider fills its parent's width and its height follows the
           canvas aspect ratio. Each content lock is pinned to the
           slider's current pixel width, so the images and hotspots scale
           together instead of squishing when a clipping window narrows.
        ---------------------------------------------------------- */
        var syncSize = function () {
            var width = slider.clientWidth;

            if (!width) {
                return;
            }

            slideEls.forEach(function (layer) {
                var lock = layer.querySelector('.comparison_lock');

                if (lock) {
                    lock.style.width = width + 'px';
                }
            });

            // Re-measure the labels against the new width
            updateLayers();
        };

        // Observe the slider itself so it refits on ANY size change —
        // parent container resizing, Contao column reflow, etc.
        if (window.ResizeObserver) {
            new window.ResizeObserver(syncSize).observe(slider);
        } else {
            window.addEventListener('resize', syncSize);
        }

        updateLayers();
        syncSize();
    };

    var initAll = function () {
        var galleries = document.querySelectorAll('.comparison_gallery');
        Array.prototype.forEach.call(galleries, initGallery);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
