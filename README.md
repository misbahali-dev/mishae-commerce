# ⚜️ MiSHAÉ — Luxury Fabrics E-Commerce

> A full-stack bespoke luxury textile e-commerce web application built with **PHP**, **MySQL**, and **Bootstrap**.

---

## 🚀 Tech Stack
* **Backend:** PHP 8.x (Session Auth, Prepared Statements, CRUD)
* **Database:** MySQL / MariaDB (Relational with Foreign Keys)
* **Frontend:** HTML5, CSS3, JavaScript, jQuery, Bootstrap (4 & 5)
* **Server:** Apache (XAMPP)

---

## ✨ Key Features

### 🛍️ Client Storefront (`/user`)
* **Bespoke Catalog & Filters:** Browse fabrics by Category (Men, Women, Kids) & Season (Summer, Winter).
* **Live Catalog Search:** Instant slide-down search without shifting navigation.
* **Shopping Bag & Buy Now:** Real-time quantity & subtotal calculation, coupon support (`MISHAE15`).
* **Wishlist System:** One-click save to wishlist with luxury toast notifications.
* **Customer Account:** Order tracking, printable invoices, and optional profile picture upload with custom confirmation modal.

### 🛡️ Admin Management Panel (`/admin`)
* **Dashboard Overview:** Real-time metrics (Total Products, Categories, Orders, Customers, and Revenue).
* **Product & Category CRUD:** Add, edit, delete products with auto image uploads; one-click category active/inactive toggle.
* **Order Management:** Track and update order fulfillment status (`Pending` → `Delivered`).
* **Client Messages:** View customer inquiries with direct WhatsApp & Email reply buttons.
* **Admin Profile:** Update credentials with optional administrator profile picture.

---

## 🛠️ How to Run Locally

1. **Clone / Copy** this repository into your XAMPP folder:  
   `C:/xampp/htdocs/commerce`
2. **Start XAMPP:** Turn ON **Apache** and **MySQL**.
3. **Import Database:**
   * Open `http://localhost/phpmyadmin/`
   * Create database: `commerce`
   * Import file: `db/commerce.sql`
4. **Open in Browser:**
   * Storefront: `http://localhost/commerce/user/home.php`
   * Admin Panel: `http://localhost/commerce/admin/index.php`

---

## 🔑 Demo Login Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@mishae.com` | `admin123` |


---

## 👩‍💻 Author
**Misbah Ali** — *Full-Stack Web Developer*
