// Admin dashboard functionality

let allProducts = [];
let allOrders = [];

function initAdminDashboard() {
    if (!requireAdmin()) return;
    
    loadAllProducts();
    loadAllOrders();
}

// load products for table
async function loadAllProducts() {
    try {
        const response = await fetch('php/products/get_products.php');
        const data = await response.json();
        
        if (data.success) {
            allProducts = data.products;
            displayProductsTable(allProducts);
        } else {
            showAdminError('Could not load products');
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showAdminError('Failed to load products');
    }
}

function displayProductsTable(products) {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No products found</td></tr>';
        return;
    }
    
    products.forEach(product => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product.id}</td>
            <td><img src="${product.image_path}" alt="${product.name}" class="product-thumb"></td>
            <td>${product.name}</td>
            <td>${product.category}</td>
            <td>$${parseFloat(product.price).toFixed(2)}</td>
            <td>
                <div class="action-btns">
                    <button class="delete-btn" onclick="deleteProduct(${product.id})">Delete</button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// add product
async function addProduct(formData) {
    try {
        const response = await fetch('php/admin/add_product.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAdminSuccess('Product added!');
            loadAllProducts();
            return true;
        } else {
            showAdminError(data.message || 'Could not add product');
            return false;
        }
    } catch (error) {
        console.error('Error adding product:', error);
        showAdminError('Failed to add product');
        return false;
    }
}

// delete product
async function deleteProduct(productId) {
    if (!confirm('Delete this product? This cannot be undone.')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        
        const response = await fetch('php/admin/delete_product.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAdminSuccess('Product deleted');
            loadAllProducts();
        } else {
            showAdminError(data.message || 'Could not delete');
        }
    } catch (error) {
        console.error('Error deleting product:', error);
        showAdminError('Delete failed');
    }
}

// load orders
async function loadAllOrders() {
    try {
        const response = await fetch('php/orders/get_orders.php');
        const data = await response.json();
        
        if (data.success) {
            allOrders = data.orders;
            displayOrdersTable(allOrders);
        } else {
            console.log('No orders yet');
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        showAdminError('Could not load orders');
    }
}

function displayOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    const noOrders = document.getElementById('noOrders');
    const table = document.getElementById('ordersTable');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (orders.length === 0) {
        if (noOrders) noOrders.style.display = 'block';
        if (table) table.style.display = 'none';
        return;
    }
    
    if (noOrders) noOrders.style.display = 'none';
    if (table) table.style.display = 'table';
    
    orders.forEach(order => {
        const orderDate = new Date(order.order_date).toLocaleDateString();
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${order.id}</td>
            <td>${order.customer_name}</td>
            <td>${order.customer_email}</td>
            <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
            <td>${orderDate}</td>
            <td><span class="order-status">${order.status}</span></td>
        `;
        tbody.appendChild(row);
    });
}

// message helpers
function showAdminSuccess(message) {
    const alertDiv = document.getElementById('alertMessage');
    if (alertDiv) {
        alertDiv.textContent = message;
        alertDiv.style.display = 'block';
        
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function showAdminError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// filter products
function filterProductsByCategory(category) {
    if (category === '') {
        displayProductsTable(allProducts);
    } else {
        const filtered = allProducts.filter(p => p.category === category);
        displayProductsTable(filtered);
    }
}

// search products
function searchProducts(searchTerm) {
    const filtered = allProducts.filter(p => 
        p.name.toLowerCase().includes(searchTerm.toLowerCase())
    );
    displayProductsTable(filtered);
}

// export to csv - handy for reports
// TODO: add date range filter for exports
function exportOrdersToCSV() {
    if (allOrders.length === 0) {
        alert('No orders to export');
        return;
    }
    
    let csv = 'Order ID,Customer,Email,Total,Date,Status\n';
    
    allOrders.forEach(order => {
        const date = new Date(order.order_date).toLocaleDateString();
        csv += `${order.id},"${order.customer_name}","${order.customer_email}",${order.total_amount},${date},${order.status}\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'orders.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
