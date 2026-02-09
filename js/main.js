/**
 * Main JavaScript File
 * Common utilities and functions used across the site
 */

// Update cart count in navigation
async function updateCartCount() {
    try {
        // Check if user is logged in
        const userData = sessionStorage.getItem('user');
        if (!userData) {
            document.getElementById('cartCount').textContent = '0';
            return;
        }
        
        const response = await fetch('php/cart/get_cart.php');
        const data = await response.json();
        
        if (data.success) {
            const count = data.count || 0;
            document.getElementById('cartCount').textContent = count;
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

// Format price with currency
function formatPrice(price) {
    return `$${parseFloat(price).toFixed(2)}`;
}

// Show toast notification (simple version)
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${type === 'success' ? '#4CAF50' : '#dc3545'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Add simple animations
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

// Global error handler for fetch requests
async function handleFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        return { success: false, message: 'Network error occurred' };
    }
}

// Debounce function for search/filter
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

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('cartCount')) {
        updateCartCount();
    }
});
