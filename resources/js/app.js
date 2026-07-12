import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
import 'datatables.net-bs5';
import Chart from 'chart.js/auto';

window.bootstrap = window.bootstrap || bootstrap;
window.Alpine = window.Alpine || Alpine;
window.Chart = window.Chart || Chart;
window.$ = window.jQuery = $;

// Start Alpine
Alpine.start();

// Theme management
const theme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-bs-theme', theme);

// Dark mode toggle
window.toggleTheme = function() {
    const current = document.documentElement.getAttribute('data-bs-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
};

// Fullscreen toggle
window.toggleFullScreen = function() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
};

// Sidebar toggle
window.toggleSidebar = function() {
    const isMobile = window.innerWidth < 992;
    if (isMobile) {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    } else {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
    }
};

// Initialize sidebar state
if (localStorage.getItem('sidebar-collapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
}

// Global AJAX setup
if (window.$) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    });
}

// Toast notifications
window.showToast = function(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toastEl);

    setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => toastEl.remove(), 300);
    }, 5000);
};

// Confirm delete helper
window.confirmDelete = function(url, name = 'this item') {
    if (confirm(`Are you sure you want to delete ${name}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
};

// Print helper
window.printElement = function(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { padding: 20px; }
                }
            </style>
        </head>
        <body>
            ${element.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
};

// Export object for use in other modules
export { bootstrap, Alpine, Chart };
