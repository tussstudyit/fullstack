// =============================================
// MAIN JAVASCRIPT FILE
// =============================================

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
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
// NOTIFICATION SYSTEM
// =============================================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
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
