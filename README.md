# Louis Vuitton E-Commerce Platform 

A full-stack e-commerce application mimicking a luxury retail experience. Built with vanilla PHP, MySQL, and modern JavaScript (ES6+).

## 🚀 Key Features

*   **Secure Authentication:** User registration/login with **Bcrypt password hashing**.
*   **Database-Persisted Cart:** Shopping carts are stored in MySQL and sync across devices (session-independent).
*   **Admin Dashboard:**
    *   Full CRUD operations for Products (Add, Edit, Delete).
    *   Secure image upload handling (whitelist validation).
    *   Order management and tracking.
*   **Role-Based Access Control (RBAC):** distinct `customer` and `admin` roles.
*   **Asynchronous UX:** Seamless "Add to Cart" and updates using **Fetch API** (no page reloads).
*   **Responsive Design:** Mobile-first CSS layout.

## 🛠️ Tech Stack

*   **Frontend:** HTML5, CSS3, JavaScript (ES6+)
*   **Backend:** PHP 7.4+ (OOP, PDO/MySQLi)
*   **Database:** MySQL (Normalized Schema: Users, Products, Cart, Orders)
*   **Server:** Apache (XAMPP/LAMP)

## 📂 Project Structure

```bash
lv-ecommerce/
├── css/             # Stylesheets (main, shop, cart, admin)
├── js/              # JavaScript modules (Auth, Cart AJAX, Admin CRUD)
├── php/             # Backend Logic
│   ├── db.php       # Database connection
│   ├── auth/        # Login, Register, Session management
│   ├── cart/        # API endpoints for Cart operations
│   └── admin/       # Product management API
├── database/        # SQL Schema (setup.sql)
├── uploads/         # Product images
└── index.html       # Entry point
```

## 📦 Installation

1.  **Clone the repo:**
    ```bash
    git clone https://github.com/nahiketema/lv-ecommerce.git
    ```
2.  **Set up the Database:**
    *   Import `database/setup.sql` into your MySQL server (via phpMyAdmin or CLI).
    *   Configure `php/db.php` with your DB credentials.
3.  **Run:**
    *   Serve the project folder using XAMPP/Apache.
    *   Visit `http://localhost/lv-ecommerce` in your browser.

## 🎬 Demo

[![Watch the demo](https://img.youtube.com/vi/Hx_4S_KviNU/maxresdefault.jpg)](https://youtu.be/Hx_4S_KviNU)

<video src="https://youtu.be/Hx_4S_KviNU" controls="controls" style="max-width: 100%;">
</video>

## 📄 License

This project is open-source and available under the MIT License.
