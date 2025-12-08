// Notification Dropdown Functionality
let notificationDropdown = null;
let notificationData = [];

function toggleNotificationDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    
    if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    } else {
        // Close user menu if open
        const userMenu = document.getElementById('userDropdownMenu');
        if (userMenu) {
            userMenu.style.display = 'none';
        }
        
        dropdown.classList.add('show');
        loadNotifications();
    }
}

function loadNotifications() {
    const basePath = getBasePath();
    
    fetch(basePath + 'Controllers/NotificationController.php?action=getNotifications')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationData = data.notifications;
                renderNotifications(data.notifications);
                updateNotificationBadge(data.unread_count);
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function renderNotifications(notifications) {
    const listContainer = document.getElementById('notificationList');
    
    if (!notifications || notifications.length === 0) {
        listContainer.innerHTML = `
            <div class="notification-empty">
                <i class="fas fa-bell-slash"></i>
                <p>Không có thông báo</p>
            </div>
        `;
        return;
    }
    
    listContainer.innerHTML = notifications.map(notif => {
        const iconClass = getNotificationIconClass(notif.type);
        const timeAgo = formatTimeAgo(notif.created_at);
        const unreadClass = !notif.is_read ? 'unread' : '';
        
        return `
            <a href="${notif.link || '#'}" class="notification-dropdown-item ${unreadClass}" onclick="markNotificationAsRead(${notif.id}, event)">
                <div class="notif-item-header">
                    <div class="notif-icon ${iconClass.className}">
                        <i class="fas ${iconClass.icon}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">${escapeHtml(notif.title)}</div>
                        ${notif.message ? `<div class="notif-message">${escapeHtml(notif.message)}</div>` : ''}
                        <div class="notif-time">${timeAgo}</div>
                    </div>
                </div>
            </a>
        `;
    }).join('');
}

function getNotificationIconClass(type) {
    const icons = {
        'comment': { className: 'info', icon: 'fa-comment' },
        'rating': { className: 'success', icon: 'fa-star' },
        'reply': { className: 'info', icon: 'fa-reply' },
        'message': { className: 'success', icon: 'fa-envelope' },
        'post_like': { className: 'info', icon: 'fa-thumbs-up' },
        'post_approved': { className: 'success', icon: 'fa-check' },
        'post_rejected': { className: 'danger', icon: 'fa-times' }
    };
    
    return icons[type] || { className: 'info', icon: 'fa-bell' };
}

function formatTimeAgo(dateString) {
    const now = new Date();
    const time = new Date(dateString);
    const diff = Math.floor((now - time) / 1000); // seconds
    
    if (diff < 60) return 'Vừa xong';
    if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
    if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
    if (diff < 604800) return Math.floor(diff / 86400) + ' ngày trước';
    
    return time.toLocaleDateString('vi-VN');
}

function markNotificationAsRead(notificationId, event) {
    const basePath = getBasePath();
    
    fetch(basePath + 'Controllers/NotificationController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=markAsRead&notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllNotificationsAsRead() {
    const basePath = getBasePath();
    
    fetch(basePath + 'Controllers/NotificationController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=markAllAsRead'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

function getBasePath() {
    const path = window.location.pathname;
    if (path.includes('/Views/')) {
        return '../../';
    }
    return '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationDropdown');
    const notificationWrapper = document.querySelector('.notification-wrapper');
    
    if (dropdown && notificationWrapper && !notificationWrapper.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Auto-refresh notifications every 30 seconds
setInterval(() => {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown && dropdown.classList.contains('show')) {
        loadNotifications();
    }
}, 30000);
