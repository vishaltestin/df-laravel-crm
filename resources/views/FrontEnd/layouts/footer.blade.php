<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © LMS.
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Design & Develop by Digital Fueled IT Pvt. Ltd. 
                </div>
            </div>
        </div>
    </div>
</footer>
<script>
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function timeAgo(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr.replace(' ', 'T'));
    const diff = Math.floor((new Date() - d) / 1000);

    if (diff < 60) return diff + " sec ago";
    if (diff < 3600) return Math.floor(diff/60) + " min ago";
    if (diff < 86400) return Math.floor(diff/3600) + " hrs ago";
    return Math.floor(diff/86400) + " days ago";
}

function renderNotificationItem(n) {

    let iconHtml = `
        <div class="avatar-xs me-3 flex-shrink-0">
            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                <i class="bx bx-bell"></i>
            </span>
        </div>
    `;

    if (n.type === 'message') {
        iconHtml = `
            <div class="avatar-xs me-3 flex-shrink-0">
                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-16">
                    <i class="bx bx-message-square-dots"></i>
                </span>
            </div>
        `;
    }

    if (n.type === 'alert') {
        iconHtml = `
            <div class="avatar-xs me-3 flex-shrink-0">
                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                    <i class="bx bx-error"></i>
                </span>
            </div>
        `;
    }

    const bg = (parseInt(n.is_read) === 0) ? 'bg-light' : '';
    const url = n.url ? n.url : 'javascript:void(0)';

    return `
    <div class="text-reset notification-item d-block dropdown-item position-relative ${bg}">
        <div class="d-flex">
            ${iconHtml}
            <div class="flex-grow-1">
                <a href="${url}" class="stretched-link" onclick="markNotificationRead(${n.id})">
                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">${n.title ?? ''}</h6>
                </a>
                <div class="fs-13 text-muted">
                    <p class="mb-1">${n.body ?? ''}</p>
                </div>
                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                    <span><i class="mdi mdi-clock-outline"></i> ${timeAgo(n.created_at)}</span>
                </p>
            </div>
            <div class="px-2 fs-15">
                <div class="form-check notification-check">
                    <input class="form-check-input noti-check" type="checkbox" value="${n.id}">
                </div>
            </div>
        </div>
    </div>`;
}

function updateCountsUI(res) {

    // top new text
    document.getElementById('notiNewText').innerText = res.unread + " New";

    // All count
    document.getElementById('notiAllCount').innerText = res.counts?.all ?? 0;

    // bell badge
    const badge = document.getElementById('notiBellBadge');
    const countEl = document.getElementById('notiBellCount');

    if (parseInt(res.unread) > 0) {
        badge.style.display = 'inline-block';
        countEl.innerText = res.unread;
    } else {
        badge.style.display = 'none';
        countEl.innerText = 0;
    }
}

function loadNotifications(type = 'all') {

    fetch("{{ route('notifications.list') }}?type=" + type)
        .then(r => r.json())
        .then(res => {

            if (!res.status) return;

            updateCountsUI(res);

            let targetId = "all-noti-list";
            if (type === 'message') targetId = "messages-noti-list";
            if (type === 'alert') targetId = "alerts-noti-list";

            const box = document.getElementById(targetId);

            if (!res.items || res.items.length === 0) {
                box.innerHTML = `<div class="p-3 text-center text-muted">No notifications</div>`;
                return;
            }

            box.innerHTML = res.items.map(renderNotificationItem).join("");
        });
}

function markNotificationRead(id) {

    fetch("{{ url('notifications/read') }}/" + id, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken()
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(res => {
        // refresh all tab counts
        loadNotifications('all');
    });
}

function markAllNotificationsRead() {

    fetch("{{ route('notifications.read_all') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken()
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(res => {
        loadNotifications('all');
        loadNotifications('message');
        loadNotifications('alert');
    });
}

function markSelectedNotificationsRead() {

    const ids = Array.from(document.querySelectorAll('.noti-check:checked'))
        .map(x => x.value);

    if (ids.length === 0) return;

    fetch("{{ route('notifications.read_bulk') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken()
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(r => r.json())
    .then(res => {
        loadNotifications('all');
        loadNotifications('message');
        loadNotifications('alert');
    });
}

// dropdown open होते ही auto load
document.addEventListener("DOMContentLoaded", function () {

    // first load counts
    loadNotifications('all');

    // जब bell icon पर click हो तब refresh
    const btn = document.getElementById("page-header-notifications-dropdown");
    if (btn) {
        btn.addEventListener("click", function () {
            loadNotifications('all');
        });
    }
});
</script>
