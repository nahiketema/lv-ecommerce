/**
 * Shop Page JavaScript
 * Handles product filtering and display
 */

// Global variable to store current filter
let currentFilter = '';

// Initialize shop page
function initShop() {
    setupCategoryFilters();
    loadProducts();
}

// Setup category filter buttons
function setupCategoryFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Get selected category
            const category = this.getAttribute('data-category');
            currentFilter = category;
            
            // Load filtered products
            loadProducts(category);
        });
    });
}

// Load products from API
async function loadProducts(category = '') {
    try {
        // Show loading state
        const grid = document.getElementById('productsGrid');
        if (grid) {
            grid.innerHTML = '<div class="loading">Loading...</div>';
        }
        
        // Build URL with category filter
        const url = category 
            ? `php/products/get_products.php?category=${encodeURIComponent(category)}`
            : 'php/products/get_products.php';
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.products.length > 0) {
            displayProducts(data.products);
        } else {
            grid.innerHTML = '<div class="empty-state"><h3>No products found</h3></div>';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        const grid = document.getElementById('productsGrid');
        if (grid) {
            grid.innerHTML = '<div class="empty-state"><h3>Error loading products</h3></div>';
        }
    }
}

// Display products in grid
function displayProducts(products) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    products.forEach(product => {
        const productCard = createProductCard(product);
        grid.appendChild(productCard);
    });
}

// Create product card element
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card fade-in';
    card.onclick = () => viewProduct(product.id);
    
    card.innerHTML = `
        <img src="${product.image_path}" alt="${product.name}" class="product-image">
        <div class="product-info">
            <p class="product-category">${product.category}</p>
            <h3 class="product-name">${product.name}</h3>
            <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
        </div>
    `;
    
    return card;
}

// Navigate to product details
function viewProduct(productId) {
    window.location.href = `product-details.html?id=${productId}`;
}

// Search products (if search functionality is added later)
function searchProducts(searchTerm) {
    // This can be extended later
    console.log('Search for:', searchTerm);
}
