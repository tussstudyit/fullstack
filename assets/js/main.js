// =============================================
// MAIN JAVASCRIPT FILE
// =============================================

// 🔥 GỘP TẤT CẢ INITIALIZATION VÀO 1 DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Main.js initializing...');
    
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // 🔥 Chat Badge Polling Initialization
    if (typeof window.chatBadgePollingInitialized === 'undefined') {
        window.chatBadgePollingInitialized = true;
        
        console.log('🔔 Initializing chat badge polling...');
        
        // Force update ngay lập tức khi load trang (override PHP badge)
        setTimeout(() => {
            updateNavbarBadgePolling();
        }, 100); // Delay 100ms để đảm bảo DOM đã render xong
        
        // Poll every 3 seconds
        setInterval(updateNavbarBadgePolling, 3000);
        
        console.log('✅ Chat badge polling started');
    }
    
    console.log('✅ Main.js initialized');
});

// =============================================
// IMAGE PREVIEW FUNCTIONALITY
// =============================================

function previewImages(input, previewContainer) {
    const files = input.files;
    previewContainer.innerHTML = '';

    if (files) {
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'preview-image-wrapper';
                imgWrapper.style.cssText = 'position: relative; display: inline-block; margin: 10px;';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb;';
                
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.className = 'remove-preview-btn';
                removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 18px; line-height: 1;';
                removeBtn.onclick = function() {
                    imgWrapper.remove();
                };
                
                imgWrapper.appendChild(img);
                imgWrapper.appendChild(removeBtn);
                previewContainer.appendChild(imgWrapper);
            };
            
            reader.readAsDataURL(file);
        });
    }
}

// =============================================
// FAVORITE FUNCTIONALITY
// =============================================

function toggleFavorite(postId, element) {
    // Check if user is logged in
    const isFavorited = element.classList.contains('active');
    const action = isFavorited ? 'remove' : 'add';
    
    // Send AJAX request to toggle favorite
    fetch('../../Controllers/FavoriteController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=' + action + '&post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.classList.toggle('active');
            const icon = element.querySelector('i');
            if (icon) {
                icon.classList.toggle('far');
                icon.classList.toggle('fas');
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Không thể kết nối đến server', 'error');
    });
}

// =============================================
// RATING FUNCTIONALITY
// =============================================

function setRating(rating, container) {
    const stars = container.querySelectorAll('.star-rating i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
        }
    });
    
    const ratingInput = container.querySelector('input[name="rating"]');
    if (ratingInput) {
        ratingInput.value = rating;
    }
}

// =============================================
// NAVBAR BADGE POLLING
// =============================================

function updateNavbarBadgePolling() {
    const chatLink = document.querySelector('a[href*="chat.php"]');
    if (!chatLink) return;
    
    const liElement = chatLink.closest('li');
    if (!liElement) return;
    
    // 🔥 FIX: Dùng dynamic path thay vì hardcoded ../../
    const apiPath = getApiPath() + 'api/get-unread-conversations.php';
    
    fetch(apiPath)
        .then(response => {
            if (!response.ok) {
                console.warn('⚠️ API response not OK:', response.status, apiPath);
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return; // Skip if response was not OK
            
            if (data.count !== undefined) {
                // 🔥 XÓA TẤT CẢ badges cũ (bao gồm cả PHP-rendered badge)
                const oldBadges = liElement.querySelectorAll('.notification-badge');
                oldBadges.forEach(b => b.remove());
                
                // 🔥 CHỈ TẠO badge mới nếu có unread
                if (data.count > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'notification-badge';
                    badge.id = 'chat-navbar-badge'; // 🔥 THÊM ID để dễ track
                    badge.style.cssText = 'position: absolute; top: -5px; right: -10px; background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: 700; min-width: 18px; text-align: center;';
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    liElement.appendChild(badge);
                    console.log('✅ Polling updated navbar badge:', data.count);
                } else {
                    console.log('📭 No unread messages, badge removed');
                }
            }
        })
        .catch(error => console.error('❌ Error fetching unread count:', error));
}

// 🔥 HÀM MỚI: Lấy path động dựa vào vị trí trang hiện tại
function getApiPath() {
    const path = window.location.pathname;
    
    // Nếu ở root (index.php)
    if (path === '/' || path.endsWith('/index.php') || !path.includes('/Views/')) {
        return '';
    }
    
    // Nếu ở trong Views/ (đếm số cấp thư mục)
    const viewsIndex = path.indexOf('/Views/');
    if (viewsIndex !== -1) {
        const afterViews = path.substring(viewsIndex + '/Views/'.length);
        const depth = afterViews.split('/').filter(p => p && !p.endsWith('.php')).length;
        return '../'.repeat(depth + 1);
    }
    
    // Fallback
    return '../../';
}

// =============================================
// NOTIFICATION SYSTEM
// =============================================

function showNotification(message, type = 'info') {
    // Disable notifications on specific pages (list.php, create.php, favorites.php, chat.php)
    const currentPath = window.location.pathname;
    const disableNotificationPages = [
        '/Views/posts/list.php',
        '/Views/posts/create.php',
        '/Views/user/favorites.php',
        '/Views/chat/chat.php'
    ];
    
    // Check if current page is in disable list
    for (let page of disableNotificationPages) {
        if (currentPath.includes(page)) {
            console.log('ℹ️ Notifications disabled on this page:', currentPath);
            return; // Exit without showing notification
        }
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Calculate top position based on navbar/header height
    // Check if page has a page-header element (like list.php, create.php)
    const pageHeader = document.querySelector('.page-header');
    let topPosition = '100px'; // Default for pages without page-header
    
    if (pageHeader) {
        // If page-header exists, add extra space to avoid overlap
        topPosition = '130px';
    }
    
    notification.style.cssText = `
        position: fixed;
        top: ${topPosition};
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        max-width: 350px;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    `;
    
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// =============================================
// SEARCH & FILTER FUNCTIONALITY
// =============================================

function filterPosts() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase();
    const category = document.getElementById('categoryFilter')?.value;
    const minPrice = document.getElementById('minPrice')?.value;
    const maxPrice = document.getElementById('maxPrice')?.value;
    const district = document.getElementById('districtFilter')?.value;
    
    // Build query string
    const params = new URLSearchParams();
    if (searchTerm) params.append('search', searchTerm);
    if (category) params.append('category', category);
    if (minPrice) params.append('min_price', minPrice);
    if (maxPrice) params.append('max_price', maxPrice);
    if (district) params.append('district', district);
    
    // Redirect to filtered results
    window.location.href = `posts.php?${params.toString()}`;
}

// =============================================
// FORM VALIDATION
// =============================================

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        // Skip validation for hidden fields (display: none or visibility: hidden)
        const style = window.getComputedStyle(input);
        if (style.display === 'none' || style.visibility === 'hidden') {
            return;
        }
        
        // Also check if parent form-step is hidden
        const formStep = input.closest('.form-step');
        if (formStep && !formStep.classList.contains('active')) {
            return;
        }
        
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            showFieldError(input, 'Trường này là bắt buộc');
        } else {
            input.classList.remove('error');
            removeFieldError(input);
        }
    });
    
    return isValid;
}

function showFieldError(input, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    
    const existingError = input.parentElement.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
    
    input.parentElement.appendChild(errorElement);
}

function removeFieldError(input) {
    const errorElement = input.parentElement.querySelector('.form-error');
    if (errorElement) {
        errorElement.remove();
    }
}

// =============================================
// CONFIRM DELETE
// =============================================

function confirmDelete(message = 'Bạn có chắc chắn muốn xóa?') {
    return confirm(message);
}

// =============================================
// FORMAT CURRENCY
// =============================================

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// =============================================
// DEBOUNCE FUNCTION
// =============================================

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// =============================================
// DELETE POST
// =============================================

function deletePost(postId) {
    if (!confirm('Bạn có chắc chắn muốn xóa tin đăng này?')) {
        return;
    }

    // Send delete request to controller
    fetch('../../Controllers/PostController.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'post_id=' + encodeURIComponent(postId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Đã xóa tin đăng', 'success');
            // Remove the post item from the page
            const postItem = document.querySelector(`.post-item[data-post-id="${postId}"]`);
            if (postItem) {
                postItem.remove();
            }
        } else {
            showNotification(data.message || 'Lỗi khi xóa tin đăng', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Lỗi khi xóa tin đăng', 'error');
    });
}
