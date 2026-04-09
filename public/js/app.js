/**
 * AgriLink – app.js
 * Global UI behaviours: sidebar, notifications, alerts, tables, forms
 */

(function() {
    'use strict';

    /* ── DOM ready ──────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', init);

    function init() {
        initSidebar();
        initAlertAutoDismiss();
        initNotificationDropdown();
        initConfirmDialogs();
        initTableSearch();
        initOrderFilter();
        initLiveOrderTotal();
    }

    /* ── Sidebar toggle ──────────────────────────────────────────────────── */
    function initSidebar() {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const layout = document.querySelector('.app-layout');
        const overlay = document.getElementById('sidebar-overlay');

        if (!toggleBtn || !layout) return;

        toggleBtn.addEventListener('click', () => {
            layout.classList.toggle('sidebar-open');
        });

        if (overlay) {
            overlay.addEventListener('click', () => layout.classList.remove('sidebar-open'));
        }

        // Close sidebar when a nav link is clicked on mobile
        document.querySelectorAll('.sidebar-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) layout.classList.remove('sidebar-open');
            });
        });
    }

    /* ── Auto-dismiss flash alerts ───────────────────────────────────────── */
    function initAlertAutoDismiss() {
        document.querySelectorAll('.alert[data-autodismiss]').forEach(alert => {
            const delay = parseInt(alert.dataset.autodismiss, 10) || 4000;
            setTimeout(() => dismissAlert(alert), delay);
        });

        document.querySelectorAll('.alert-close').forEach(btn => {
            btn.addEventListener('click', () => dismissAlert(btn.closest('.alert')));
        });
    }

    function dismissAlert(el) {
        if (!el) return;
        el.style.transition = 'opacity 0.3s, transform 0.3s';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => el.remove(), 350);
    }

    /* ── Notification dropdown ───────────────────────────────────────────── */
    function initNotificationDropdown() {
        const bell = document.getElementById('notif-btn');
        const dropdown = document.getElementById('notif-dropdown');
        if (!bell || !dropdown) return;

        bell.addEventListener('click', e => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', e => {
            if (!dropdown.contains(e.target) && e.target !== bell) {
                dropdown.classList.add('hidden');
            }
        });
    }

    /* ── Confirm-before-submit dialogs ──────────────────────────────────── */
    function initConfirmDialogs() {
        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', e => {
                const msg = el.dataset.confirm || 'Are you sure?';
                if (!confirm(msg)) e.preventDefault();
            });
        });

        // Delete listing confirm (form submit)
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', e => {
                const msg = form.dataset.confirm || 'Are you sure?';
                if (!confirm(msg)) e.preventDefault();
            });
        });
    }

    /* ── Generic table search (live filter rows) ─────────────────────────── */
    function initTableSearch() {
        document.querySelectorAll('[data-table-search]').forEach(input => {
            const targetId = input.dataset.tableSearch;
            const table = document.getElementById(targetId);
            if (!table) return;

            input.addEventListener('input', () => {
                const q = input.value.trim().toLowerCase();
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        });
    }

    /* ── Order status tab filter (buyer orders page) ─────────────────────── */
    function initOrderFilter() {
        const tabs = document.querySelectorAll('[data-order-filter]');
        if (!tabs.length) return;

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const status = tab.dataset.orderFilter;
                tabs.forEach(t => t.classList.remove('active-tab'));
                tab.classList.add('active-tab');

                document.querySelectorAll('.order-card').forEach(card => {
                    if (status === 'all' || card.dataset.status === status) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    /* ── Live order total calculator ─────────────────────────────────────── */
    function initLiveOrderTotal() {
        const qtyInput = document.getElementById('order-qty');
        const totalEl = document.getElementById('order-total-display');
        const priceInput = document.getElementById('price-per-unit');

        if (!qtyInput || !totalEl || !priceInput) return;

        function update() {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalEl.textContent = '₵' + total.toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        qtyInput.addEventListener('input', update);
        update(); // run once on load
    }

})();