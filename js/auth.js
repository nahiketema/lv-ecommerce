// Authentication helpers

// login
async function handleLogin(email, password) {
    try {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        
        const response = await fetch('php/auth/login.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            sessionStorage.setItem('user', JSON.stringify(data.user));
            
            // redirect based on role
            if (data.user.role === 'admin') {
                window.location.href = 'admin-dashboard.html';
            } else {
                window.location.href = 'shop.html';
            }
            
            return { success: true };
        } else {
            return { success: false, message: data.message };
        }
    } catch (error) {
        console.error('Login error:', error);
        return { success: false, message: 'Login failed. Please try again.' };
    }
}

// register new account
async function handleRegister(fullName, email, password, confirmPassword) {
    // quick validation
    if (password !== confirmPassword) {
        return { success: false, message: 'Passwords do not match' };
    }
    
    if (password.length < 6) {
        return { success: false, message: 'Password must be at least 6 characters' };
    }
    
    try {
        const formData = new FormData();
        formData.append('full_name', fullName);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('confirm_password', confirmPassword);
        
        const response = await fetch('php/auth/register.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            sessionStorage.setItem('user', JSON.stringify(data.user));
            
            setTimeout(() => {
                window.location.href = 'shop.html';
            }, 1000);
            
            return { success: true, message: 'Account created!' };
        } else {
            return { success: false, message: data.message };
        }
    } catch (error) {
        console.error('Registration error:', error);
        return { success: false, message: 'Registration failed' };
    }
}

// logout
async function handleLogout() {
    try {
        await fetch('php/auth/logout.php');
        sessionStorage.clear();
        window.location.href = 'index.html';
    } catch (error) {
        console.error('Logout error:', error);
        // still clear local data even if server call fails
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
}

// helper functions
function isLoggedIn() {
    return sessionStorage.getItem('user') !== null;
}

function getCurrentUser() {
    const userData = sessionStorage.getItem('user');
    return userData ? JSON.parse(userData) : null;
}

function isAdmin() {
    const user = getCurrentUser();
    return user && user.role === 'admin';
}

// redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        alert('Please login to continue');
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

// redirect if not admin
function requireAdmin() {
    if (!isLoggedIn()) {
        alert('Please login first');
        window.location.href = 'login.html';
        return false;
    }
    
    if (!isAdmin()) {
        alert('You need admin access for this page');
        window.location.href = 'index.html';
        return false;
    }
    
    return true;
}

// update nav links based on login status
function updateNavigation() {
    const user = getCurrentUser();
    const authLink = document.getElementById('authLink');
    const logoutLink = document.getElementById('logoutLink');
    
    if (user && authLink) {
        authLink.textContent = user.name;
        authLink.href = user.role === 'admin' ? 'admin-dashboard.html' : '#';
        
        if (logoutLink) {
            logoutLink.style.display = 'block';
            logoutLink.onclick = async (e) => {
                e.preventDefault();
                await handleLogout();
            };
        }
    }
}

// run on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNavigation();
});
