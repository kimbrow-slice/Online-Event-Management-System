// search.js
// Handles search form on index.html (redirect) and events.html (AJAX reload)

document.addEventListener('DOMContentLoaded', () => {
    const searchForm  = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    if (!searchForm || !searchInput) return;

    // Intercept form submission
    searchForm.addEventListener('submit', e => {
        e.preventDefault();
        const q = searchInput.value.trim();

        // If on index.html, redirect to events.html with query
        if (window.location.pathname.endsWith('index.html') || window.location.pathname === '/') {
            window.location.href = `events.html?q=${encodeURIComponent(q)}`;
            return;
        }

        // Otherwise (on events.html), update URL and fetch filtered results
        history.replaceState(null, '', `events.html?q=${encodeURIComponent(q)}`);
        fetchAndRender(`events_data.php?q=${encodeURIComponent(q)}`);
    });

    // On events.html load, auto-run search if q is present
    if (window.location.pathname.endsWith('events.html')) {
        const params   = new URLSearchParams(window.location.search);
        const initialQ = params.get('q') || '';
        if (initialQ) searchInput.value = initialQ;
        fetchAndRender(initialQ
            ? `events_data.php?q=${encodeURIComponent(initialQ)}`
            : 'events_data.php'
        );
    }

    // Fetch the HTML rows and insert countdown cells
    function fetchAndRender(url) {
        fetch(url, { credentials: 'include' })
            .then(r => r.text())
            .then(html => {
                const tbody = document.querySelector('#eventsTable tbody');
                tbody.innerHTML = html;

                // Insert countdown column if missing
                tbody.querySelectorAll('tr').forEach(row => {
                    if (!row.querySelector('.countdown')) {
                        const dateText = row.cells[1].textContent.trim();
                        const timeText = row.cells[2].textContent.trim();
                        const cdCell = row.insertCell(3);
                        cdCell.className = 'countdown';
                        cdCell.dataset.target = `${dateText}T${timeText}`;
                        cdCell.textContent = ''; // placeholder
                    }
                });
            })
            .catch(console.error);
    }
});