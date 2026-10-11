import './bootstrap';

import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
window.bootstrap = bootstrap;

import Chart from 'chart.js/auto';
window.Chart = Chart;

/**
 * Utilidades de CampusShop (JS puro, sin frameworks SPA).
 */
const cs = {
    /** Cierra el menú móvil de Bootstrap automáticamente al navegar. */
    initDropdownAuto: () => {
        document.querySelectorAll('.dropdown-menu a').forEach((link) => {
            link.addEventListener('click', () => {
                const menu = link.closest('.dropdown-menu');
                bootstrap.Dropdown.getInstance(menu)?.hide();
            });
        });
    },

    /** Petición fetch con token CSRF (RNF-03). */
    fetch: async (url, options = {}) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', ...(options.headers || {}) };
        const res = await fetch(url, { ...options, headers });
        if (!res.ok) {
            let message = 'Ocurrió un error inesperado.';
            try {
                const data = await res.json();
                message = data.message || data.error || message;
            } catch (e) { /* cuerpo no JSON */ }
            throw new Error(message);
        }
        return res.json();
    },

    /** Polling del chat y del contador de no leídos (RF-33). */
    initChatPolling: () => {
        const container = document.getElementById('chat-messages');
        const interval = Number((container?.dataset.pollSeconds) || 10) * 1000;

        if (container) {
            const url = container.dataset.pollUrl;
            const poll = async () => {
                const ultimo = Number(container.dataset.lastId || 0);
                try {
                    const data = await cs.fetch(`${url}?ultimo=${ultimo}`);
                    (data.mensajes || []).forEach((m) => cs.appendMessage(container, m));
                    if (data.mensajes && data.mensajes.length) {
                        container.dataset.lastId = data.ultimo;
                        container.scrollTop = container.scrollHeight;
                    }
                } catch (e) { /* silencioso en cada ciclo */ }
            };
            setInterval(poll, interval);
        }

        const badge = document.getElementById('mensajes-no-leidos');
        if (badge) {
            setInterval(async () => {
                try {
                    const data = await cs.fetch('/conversaciones/no-leidos');
                    const total = Number(data.no_leidos || 0);
                    badge.textContent = total;
                    badge.classList.toggle('d-none', total === 0);
                } catch (e) { /* silencioso */ }
            }, interval);
        }
    },

    appendMessage: (container, m) => {
        const wrap = document.createElement('div');
        wrap.className = `d-flex ${m.mine ? 'justify-content-end' : 'justify-content-start'} mb-3`;
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${m.mine ? 'mine' : 'theirs'}`;
        bubble.innerHTML = `${m.contenido}<div class="time">${m.hora}</div>`;
        wrap.appendChild(bubble);
        container.appendChild(wrap);
    },

    /** Galería de producto: cambia la imagen principal al pulsar una miniatura. */
    initGallery: () => {
        const main = document.getElementById('galeria-principal');
        if (!main) return;
        document.querySelectorAll('.galeria-miniatura').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                main.src = thumb.dataset.src;
                document.querySelectorAll('.galeria-miniatura').forEach((t) => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
    },

    /** Selector de cantidad (- / +). */
    initQty: () => {
        document.querySelectorAll('[data-qty-menos]').forEach((btn) => {
            const input = btn.parentElement.querySelector('[data-qty-input]');
            btn.addEventListener('click', () => {
                const min = Number(input.min || 1);
                input.value = Math.max(min, Number(input.value) - 1);
            });
        });
        document.querySelectorAll('[data-qty-mas]').forEach((btn) => {
            const input = btn.parentElement.querySelector('[data-qty-input]');
            btn.addEventListener('click', () => {
                const max = Number(input.max || Infinity);
                input.value = Math.min(max, Number(input.value) + 1);
            });
        });
    },

    /** Vista previa de imágenes: [data-preview] (una) y [data-preview-multi] (varias). */
    initImagePreview: () => {
        const toast = (msg, type = 'danger') => {
            const cont = document.querySelector('[data-toasts]');
            if (!cont) return;
            cont.insertAdjacentHTML(
                'beforeend',
                `<div class="toast align-items-center text-bg-${type} border-0 show" role="alert">
                  <div class="d-flex"><div class="toast-body">${msg}</div>
                  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
                </div>`
            );
            const el = cont.lastElementChild;
            new bootstrap.Toast(el).show();
        };

        document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
            const target = document.querySelector(input.dataset.preview);
            if (!target) return;
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) return;
                target.src = URL.createObjectURL(file);
                target.classList.remove('d-none');
            });
        });

        document.querySelectorAll('input[type="file"][data-preview-multi]').forEach((input) => {
            const target = document.querySelector(input.dataset.previewMulti);
            if (!target) return;
            const max = Number(input.dataset.max || 5);
            input.addEventListener('change', () => {
                target.innerHTML = '';
                if (input.files.length > max) {
                    toast(`Puedes subir hasta ${max} imágenes.`);
                }
                Array.from(input.files).slice(0, max).forEach((file) => {
                    if (!file.type.match(/^image\/(jpeg|png|webp)$/)) {
                        toast(`«${file.name}» no es jpg, png o webp.`);
                        return;
                    }
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-thumbnail me-2 mb-2';
                    img.style.width = '84px';
                    img.style.height = '84px';
                    img.style.objectFit = 'cover';
                    target.appendChild(img);
                });
            });
        });
    },

    /** Modal de confirmación reutilizable: botón [data-confirm] con data-action/data-mensaje. */
    initConfirm: () => {
        const modalEl = document.getElementById('modal-confirmar');
        if (!modalEl) return;
        const form = modalEl.querySelector('form');
        const body = modalEl.querySelector('[data-confirm-text]');
        document.querySelectorAll('[data-confirm]').forEach((btn) => {
            btn.addEventListener('click', () => {
                form.action = btn.dataset.action;
                if (btn.dataset.mensaje) body.textContent = btn.dataset.mensaje;
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });
    },

    /** Inicializa los gráficos Chart.js marcados con [data-chart]. */
    initCharts: () => {
        document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
            let config;
            try {
                config = JSON.parse(canvas.dataset.chart);
            } catch (e) {
                return;
            }
            new Chart(canvas, config);
        });
    },
};

window.cs = cs;

document.addEventListener('DOMContentLoaded', () => {
    cs.initDropdownAuto();
    cs.initChatPolling();
    cs.initGallery();
    cs.initQty();
    cs.initImagePreview();
    cs.initConfirm();
    cs.initCharts();
});
