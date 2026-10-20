# ZakatPro — Web-Based Zakat Management System

**Final Year Software Engineering Project**
*Development of a Web-Based Zakat calculation and management system*

---

## 🗂 Project Structure

```
athan/
├── admin/
│   └── dashboard.php          — Admin analytics dashboard
├── auth/
│   ├── login.php              — User login
│   ├── register.php           — User registration
│   └── logout.php             — Session destruction
├── config/
│   ├── db.php                 — PDO database connection
│   ├── zakat.php              — Zakat calculation logic & constants
│   └── database.sql           — Full SQL schema + seed data
├── includes/
│   ├── functions.php          — Auth helpers, flash messages, sanitizers
│   ├── header.php             — Shared HTML <head> + Tailwind CSS
│   ├── footer.php             — Shared footer
│   ├── navbar.php             — Top navigation bar
│   └── flash.php              — Flash message display partial
├── user/
│   ├── dashboard.php          — User home dashboard
│   ├── calculator.php         — Zakat calculator with results
│   ├── history.php            — Full calculation history
│   ├── profile.php            — Profile + password change
│   └── report.php             — Print-to-PDF Zakat certificate
├── assets/
│   ├── css/                   — Custom stylesheets (if any)
│   └── js/                    — Custom scripts (if any)
├── .htaccess                  — Apache security rules
└── index.php                  — Public landing page
```

---

## ⚙️ Setup Instructions

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.4+
- Apache with `mod_rewrite` enabled
- XAMPP / WAMP / LAMP / MAMP

### Step 1 — Import Database
1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Click **Import**
3. Select `athan/config/database.sql`
4. Click **Go**

### Step 2 — Configure Database
Edit `config/database.php` and update:
```php
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASS', '');         // Your MySQL password
```

### Step 3 — Place in Web Root
Copy the `athan/` folder to your web server root:
- XAMPP: `C:/xampp/htdocs/athan/`
- WAMP: `C:/wamp64/www/athan/`
- Linux/Mac: `/var/www/html/athan/`

### Step 4 — Access the System
- Landing Page: http://localhost/athan/
- Login: http://localhost/athan/auth/login.php
- Admin: http://localhost/athan/admin/dashboard.php

### Default Admin Credentials
```
Email:    admin@zakatpro.com
Password: Admin@1234
```

---

## 🧮 Zakat Calculation Formula

```
Net Zakatable Wealth = Cash + Gold Value + Silver Value + Business Assets − Debts

If Net Zakatable Wealth ≥ Nisab Threshold (85g × Gold Price):
    Zakat Due = Net Zakatable Wealth × 2.5%
Else:
    No Zakat Obligatory
```

Current Nisab (configurable in `Logic/agriculture_logic.php`):
- Gold price per gram: ₦80,000
- Nisab = 85 × ₦80,000 = **₦6,800,000**

---

## 🔒 Security Features
- Passwords hashed with **bcrypt** (cost factor 12)
- All DB queries use **PDO prepared statements** (SQL injection prevention)
- Session regeneration on login (session fixation prevention)
- Input sanitization on all form fields
- Apache directory listing disabled via `.htaccess`
- Role-based access control (user/admin)
- HttpOnly session cookies

---

## 🧰 Technology Stack

| Layer     | Technology              |
|-----------|-------------------------|
| Frontend  | HTML5, Tailwind CSS CDN |
| Backend   | PHP 8.0+ (OOP/PDO)      |
| Database  | MySQL 5.7+              |
| Charts    | Chart.js v4             |
| Fonts     | Google Fonts (Nunito)   |
| PDF       | Browser Print → PDF     |

---

## 📄 PDF Reports

Reports are generated as print-optimised HTML pages.  
Users press **"Save as PDF"** which opens the browser's native Print dialog → Save as PDF.

To integrate DomPDF for server-side PDF generation:
```bash
composer require dompdf/dompdf
```
Then uncomment the DomPDF section in `user/report.php`.

---

## 📊 Academic Information

This project demonstrates:
- **Full-stack web development** (PHP + MySQL + HTML/CSS)
- **OOP principles** (PDO class, modular structure)
- **Secure coding practices** (OWASP guidelines)
- **Database design** (normalised relational schema)
- **Islamic finance domain knowledge** (Nisab, Hawl, Zakat rate)
- **Data visualisation** (Chart.js integration)
- **User experience design** (Tailwind CSS, responsive layout)


## User Area (current implementation)
The user dashboard is intentionally kept simple around the main Zakat journey:
Dashboard -> Calculate -> Save -> Submit -> Review -> Payment -> Certificate.

Additional user pages:
- user/calculations.php
- user/calculation-view.php
- user/status.php
- user/reports.php
- user/report.php
- user/payment.php
- user/certificate.php
- user/notifications.php
- user/profile.php
- user/history.php
- user/donation.php

The database schema in `database file/decission.sql` now contains the tables required for the user workflow (reports, payments, certificates, donations and calculation review status).
