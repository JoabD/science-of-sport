// Landing page (event directory) behavior: the events table + pagination,
// the "view details" modal, and re-opening the auth/profile modal after a
// redirect back from the server (login errors, profile saved, etc).
//
// This used to be a <script> block inside posts/show.blade.php.

document.addEventListener('DOMContentLoaded', function () {
    const isLoggedIn = document.body.dataset.loggedIn === '1';
    const isAdmin = document.body.dataset.isAdmin === '1';

    initEventsTable(isLoggedIn, isAdmin);
    initEventFormModal();
    reopenModalAfterRedirect();
});

function showAuthToast(message) {
    document.getElementById('toastMessage').innerText = message;
    const toastEl = document.getElementById('authToast');
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Called from inline onclick="" attributes in the blade views, so it has to
// be global.
window.showAuthToast = showAuthToast;

function initEventsTable(isLoggedIn, isAdmin) {
    const container = document.getElementById('eventsContainer');
    const paginationList = document.getElementById('paginationList');
    const paginationNav = document.getElementById('paginationNav');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    if (!container) return;

    let currentPage = 1;

    const loadPage = (page) => {
        container.style.opacity = '0.5';
        loadingIndicator.style.display = 'block';

        fetch(`/api/events?page=${page}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderEvents(data.data);
                    renderPagination(data.current_page, data.last_page);
                    currentPage = data.current_page;
                }
            })
            .finally(() => {
                container.style.opacity = '1';
                loadingIndicator.style.display = 'none';
            });
    };

    const renderEvents = (events) => {
        container.innerHTML = '';

        if (events.length === 0) {
            container.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No events found.</td></tr>';
            return;
        }

        events.forEach(event => {
            const dateObj = new Date(event.event_date);
            const dateStr = isNaN(dateObj) ? event.event_date : dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

            // Action buttons based on auth state and role
            let actionButtons = '';
            if (!isLoggedIn) {
                actionButtons = `<button class="btn btn-sm btn-warning shadow-sm fw-bold" onclick="showAuthToast('Please log in to view event details.')">View Details</button>`;
            } else {
                actionButtons = `<button class="btn btn-sm text-white shadow-sm fw-bold bg-brand-blue view-details-btn me-1" data-id="${event.id}">View Details</button>`;

                if (isAdmin) {
                    actionButtons += `
                        <button class="btn btn-sm btn-outline-secondary shadow-sm fw-bold edit-event-btn me-1" data-id="${event.id}">Edit</button>
                        <form method="POST" action="/events/${event.id}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm fw-bold">Delete</button>
                        </form>
                    `;
                }
            }

            const rowHtml = `
                <tr>
                    <td class="fw-bold px-4 text-nowrap event-date-cell">${dateStr}</td>
                    <td>
                        <span class="fw-bold event-title-cell">${event.title}</span><br>
                        <small class="text-muted">${event.subtitle || ''}</small>
                    </td>
                    <td>${event.location}</td>
                    <td class="text-center">
                        ${actionButtons}
                    </td>
                </tr>
            `;
            container.innerHTML += rowHtml;
        });

        container.querySelectorAll('.view-details-btn').forEach(btn => {
            btn.addEventListener('click', () => loadEventDetails(btn.dataset.id));
        });
        container.querySelectorAll('.edit-event-btn').forEach(btn => {
            btn.addEventListener('click', () => openEditEventModal(btn.dataset.id));
        });
    };

    const renderPagination = (current, last) => {
        paginationList.innerHTML = '';
        if (last <= 1) {
            paginationNav.style.display = 'none';
            return;
        }
        paginationNav.style.display = 'block';

        const prevDisabled = current === 1 ? 'disabled' : '';
        paginationList.innerHTML += `<li class="page-item ${prevDisabled}"><a class="page-link" href="#" data-page="${current - 1}">Previous</a></li>`;

        for (let i = 1; i <= last; i++) {
            const activeClass = current === i ? 'active' : '';
            paginationList.innerHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        const nextDisabled = current === last ? 'disabled' : '';
        paginationList.innerHTML += `<li class="page-item ${nextDisabled}"><a class="page-link" href="#" data-page="${current + 1}">Next</a></li>`;

        const pageLinks = paginationList.querySelectorAll('.page-link');
        pageLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                if (this.parentElement.classList.contains('disabled') || this.parentElement.classList.contains('active')) return;
                const pageToLoad = parseInt(this.getAttribute('data-page'));
                if (!isNaN(pageToLoad)) loadPage(pageToLoad);
            });
        });
    };

    loadPage(currentPage);
}

function loadEventDetails(id) {
    const modalEl = document.getElementById('eventDetailsModal');
    const body = document.getElementById('eventDetailsBody');
    const modal = new bootstrap.Modal(modalEl);

    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border" style="color: var(--sos-blue);" role="status"></div></div>';
    modal.show();

    fetch(`/api/posts/${id}/packages`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                body.innerHTML = '<p class="text-danger mb-0">This event could not be loaded.</p>';
                return;
            }
            body.innerHTML = renderEventDetails(data.post, data.packages);
        })
        .catch(() => {
            body.innerHTML = '<p class="text-danger mb-0">This event could not be loaded.</p>';
        });
}

function renderEventDetails(post, packages) {
    let html = `
        <h4 class="text-brand-blue fw-bold">${post.title}</h4>
        <p class="text-muted">${post.subtitle || ''}</p>
        <p>${post.overview}</p>
    `;

    if (packages.length === 0) {
        return html + '<p class="text-muted mb-0">No packages have been added to this event yet.</p>';
    }

    html += '<div class="row g-3 mt-2">';
    packages.forEach(pkg => {
        html += `
            <div class="col-md-6">
                <div class="package-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="fw-bold mb-1">${pkg.name}</h6>
                        <span class="badge bg-brand-green">${pkg.type}</span>
                    </div>
                    <div class="price">$${Number(pkg.price).toLocaleString()}</div>
                    ${pkg.capacity ? `<small class="text-muted">Capacity: ${pkg.capacity}</small><br>` : ''}
                    ${pkg.description ? `<p class="mt-2 mb-0 small">${pkg.description}</p>` : ''}
                </div>
            </div>
        `;
    });
    html += '</div>';

    return html;
}

// The create-event modal doubles as the edit-event modal: clicking "Edit"
// fetches the event, drops it into the same form and points the form at the
// update route instead of the store route.
function openEditEventModal(id) {
    fetch(`/api/posts/${id}/packages`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) fillEventForm(data.post);
        });
}

function fillEventForm(post) {
    const modalEl = document.getElementById('createEventModal');
    const form = modalEl.querySelector('form');

    form.action = `/events/${post.id}`;
    form.querySelector('[name="_method"]').value = 'PUT';
    form.querySelector('[name="post_id"]').value = post.id;

    form.querySelector('#title').value = post.title || '';
    form.querySelector('#subtitle').value = post.subtitle || '';
    form.querySelector('#event_date').value = post.event_date || '';
    form.querySelector('#location').value = post.location || '';
    form.querySelector('#overview').value = post.overview || '';

    modalEl.querySelector('#createEventModalLabel').innerText = 'Edit Event';
    modalEl.querySelector('button[type="submit"]').innerText = 'Save Changes';

    new bootstrap.Modal(modalEl).show();
}

// Puts the shared form back into "create" mode once the modal is closed, so
// it doesn't stay stuck showing the last edited event.
function resetEventForm() {
    const modalEl = document.getElementById('createEventModal');
    if (!modalEl) return;
    const form = modalEl.querySelector('form');

    form.action = form.dataset.createAction;
    form.querySelector('[name="_method"]').value = '';
    form.querySelector('[name="post_id"]').value = '';

    ['title', 'subtitle', 'event_date', 'location', 'overview'].forEach(field => {
        const input = form.querySelector(`#${field}`);
        if (input) input.value = '';
    });

    modalEl.querySelector('#createEventModalLabel').innerText = 'Create New Event';
    modalEl.querySelector('button[type="submit"]').innerText = 'Save Event';
}

function initEventFormModal() {
    const modalEl = document.getElementById('createEventModal');
    if (!modalEl) return;

    modalEl.addEventListener('hidden.bs.modal', resetEventForm);
}

// After a redirect back (failed login, saved profile, create-event errors...)
// the blade layout stamps which modal was involved on the <body> tag, so we
// can pop it back open instead of leaving the user wondering what happened.
function reopenModalAfterRedirect() {
    const openModal = document.body.dataset.openModal;
    if (!openModal) return;

    if (openModal === 'login') {
        new bootstrap.Modal(document.getElementById('loginModal')).show();
    } else if (openModal === 'register') {
        new bootstrap.Modal(document.getElementById('registerModal')).show();
    } else if (openModal === 'create-event') {
        const modalEl = document.getElementById('createEventModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    } else if (openModal.startsWith('profile')) {
        const modalEl = document.getElementById('profileModal');
        if (!modalEl) return;
        new bootstrap.Modal(modalEl).show();

        const tab = openModal.split('-')[1] || 'info';
        const tabButton = document.getElementById(`profile-${tab}-tab`);
        if (tabButton) new bootstrap.Tab(tabButton).show();
    }
}
