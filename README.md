# UniExchange — Student to Student Book & Equipment Exchange System

A complete, colorful, OOP-PHP + MySQL web application built for the SLIATE
Individual Project (ICT Project HNDIT4052), based on the submitted SRS and
System Design report.

## Tech Stack
- **Frontend:** HTML5, CSS3 (custom colorful theme), Bootstrap 5, JavaScript, Font Awesome 6
- **Backend:** PHP 8 (pure OOP, no framework — PDO + prepared statements)
- **Database:** MySQL / MariaDB
- **Server:** XAMPP / Apache

## OOP Design
The system follows the Class Diagram from the SRS:

| Class | Responsibility |
|---|---|
| `Database` | Singleton PDO connection |
| `User` | Registration, login, profile (base class) |
| `Admin extends User` | Manage users, delete listings, view reports (**inheritance**) |
| `Category` | Category CRUD |
| `Item` | Post / edit / delete / search / filter / mark-as-sold |
| `Message` | Buyer↔seller messaging & inbox threads |
| `SavedItem` | Wishlist |
| `Report` | Reporting inappropriate listings |
| `ActivityLog` | System audit trail |
| `Auth` | Session guard helper |

## Folder Structure
```
uniexchange/
├── admin/                 Admin panel (dashboard, users, listings, reports, log)
├── assets/css/js/uploads  Static assets & uploaded images
├── classes/                OOP classes (Model layer)
├── config/config.php       DB credentials & bootstrap
├── database/uniexchange.sql   Full MySQL schema + sample data
├── includes/                Shared header/navbar/footer/alerts
├── index.php, browse.php, item-details.php, post-item.php,
│   edit-item.php, delete-item.php, mark-sold.php,
│   dashboard.php, messages.php, wishlist.php, profile.php,
│   register.php, login.php, logout.php, save-item.php
```

## Setup Instructions (XAMPP)

1. Copy the `uniexchange` folder into `C:\xampp\htdocs\` (Windows) or
   `/Applications/XAMPP/htdocs/` (macOS).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Click **Import**, choose `database/uniexchange.sql`, and click **Go**.
   This creates the `uniexchange` database with all tables and sample data.
5. Check `config/config.php` — default XAMPP credentials (`root` / no password)
   are already set. Edit `DB_USER` / `DB_PASS` if yours differ.
6. Visit `http://localhost/uniexchange/index.php` in your browser.

## Demo Login Accounts
All sample accounts use the password: **admin@123**

| Role | Email |
|---|---|
| Admin | admin@gmail.com |
| Student | sasindu@uni.lk |
| Student | nimasha@uni.lk |
| Student | kasun@uni.lk |

## Features Implemented (mapped to SRS Functional Requirements)
- **FR01** Student registration & login (with bcrypt password hashing)
- **FR02** Post items with title, category, condition, price, image upload
- **FR03** Search & filter (keyword, category, condition, price range, sort)
- **FR04** View full item details with seller info
- **FR05** Send / receive messages (inbox + live conversation thread)
- **FR06** Mark items as sold
- **FR07** User dashboard — my listings, stats, unread messages
- **FR08** Admin panel — manage users, manage listings, view reports, activity log
- **Extra:** Wishlist (save items), report inappropriate listings, edit profile,
  change password, activity logging

## Security Notes
- Passwords hashed with `password_hash()` / verified with `password_verify()`
- All SQL queries use PDO prepared statements (no SQL injection)
- Session-based authentication with `Auth::requireLogin()` / `Auth::requireAdmin()` guards
- File upload validation (type & size restrictions)
- Output escaped with `htmlspecialchars()` to prevent XSS

## Notes
- This project intentionally uses **plain OOP PHP with PDO** (no Laravel), matching
  the SRS's stated tech stack (Section 1.9 Software Requirements: "PHP without a framework").
- To extend: add payment integration, ratings/reviews, or a REST API layer as
  noted in the SRS "Out-of-Scope" / "Future Improvements" sections.
