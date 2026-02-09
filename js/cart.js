// Cart functionality
// Handles adding, removing, updating cart items

let cartData = {
    items: [],
    total: 0
};

function initCart() {
    loadCartItems();
}

// fetch cart from server
async function loadCartItems() {
    try {
        const response = await fetch('php/cart/get_cart.php');
        const data = await response.json();
        
        if (data.success) {
            cartData.items = data.cart_items;
            cartData.total = parseFloat(data.total);
            
            if (cartData.items.length > 0) {
                displayCartItems();
                updateCartSummary();
            } else {
                showEmptyCart();
            }
        } else {
            showError('Could not load cart');
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        showError('Something went wrong loading your cart');
    }
}

function displayCartItems() {
    const container = document.getElementById('cartItems');
    if (!container) return;
    
    container.innerHTML = '';
    
    cartData.items.forEach(item => {
        const cartItem = createCartItemElement(item);
        container.appendChild(cartItem);
    });
    
    document.getElementById('cartContent').style.display = 'grid';
    document.getElementById('emptyCart').style.display = 'none';
}

function createCartItemElement(item) {
    const div = document.createElement('div');
    div.className = 'cart-item fade-in';
    
    div.innerHTML = `
        <img src="${item.image_path}" alt="${item.name}" class="cart-item-image">
        <div class="cart-item-details">
            <h3 class="cart-item-name">${item.name}</h3>
            <p class="cart-item-category">${item.category}</p>
            <p class="cart-item-price">$${parseFloat(item.price).toFixed(2)} each</p>
        </div>
        <div class="cart-item-controls">
            <div class="cart-quantity-controls">
                <button class="cart-qty-btn" onclick="changeQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
                <span class="cart-qty-display">${item.quantity}</span>
                <button class="cart-qty-btn" onclick="changeQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
            </div>
            <button class="remove-btn" onclick="removeFromCart(${item.cart_id})">Remove</button>
        </div>
    `;
    
    return div;
}

function updateCartSummary() {
    document.getElementById('subtotal').textContent = `$${cartData.total.toFixed(2)}`;
    document.getElementById('total').textContent = `$${cartData.total.toFixed(2)}`;
}

function showEmptyCart() {
    document.getElementById('cartContent').style.display = 'none';
    document.getElementById('emptyCart').style.display = 'block';
}

// update quantity
async function changeQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;
    
    try {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', newQuantity);
        
        const response = await fetch('php/cart/update_cart.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadCartItems();
            updateCartCount();
        } else {
            showError(data.message || 'Could not update quantity');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showError('Failed to update');
    }
}

// remove item
async function removeFromCart(cartId) {
    if (!confirm('Remove this item from your cart?')) return;
    
    try {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        
        const response = await fetch('php/cart/remove_from_cart.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadCartItems();
            updateCartCount();
            showToast('Item removed', 'success');
        } else {
            showError(data.message || 'Could not remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        showError('Something went wrong');
    }
}

// add to cart (called from product pages)
async function addToCart(productId, quantity = 1) {
    // check login first
    const userData = sessionStorage.getItem('user');
    if (!userData) {
        alert('Please login to add items to cart');
        window.location.href = 'login.html';
        return false;
    }
    
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        const response = await fetch('php/cart/add_to_cart.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Added to cart!', 'success');
            updateCartCount();
            return true;
        } else {
            showError(data.message || 'Could not add to cart');
            return false;
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showError('Failed to add item');
        return false;
    }
}

function showError(message) {
    // TODO: make this a nicer toast notification
    alert(message);
}
