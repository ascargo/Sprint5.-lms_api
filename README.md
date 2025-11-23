# S5.01.-LMS-API

API REST exercise for bootcamp. Small Library Management System

This project is the final submission for S5.01 – Laravel REST API, demonstrating a full-featured backend with:

🔐 Authentication (login, register, logout)
👥 User roles (admin, patron)
📚 CRUD for books
🧑‍🤝‍🧑 CRUD for patrons
📘 Loan management (borrow, return, overdue detection)
📊 Dashboard stats
🔎 Filtering & pagination
🧪 Feature tests
🌱 Database seeders
📝 Auto-generated API documentation (Scribe)

🧰 Tech Stack
Category Tools
Backend Laravel 10, PHP 8.2
Authentication Laravel Passport (token-based)
Database MySQL
Documentation Scribe (Blade UI + Postman + OpenAPI)
Testing PHPUnit + Laravel TestSuite
Dev tools Artisan CLI, Composer, Git/GitHub

🚀 Features Overview

🔐 Authentication & Roles
Register and login with token-based authentication
admin role → Full access to CRUD and dashboard
patron role → Can view books & own loans only
Protect routes with:
auth:api
role:admin
custom middleware patron.owns.loan

📚 Books
Create, update, delete books (admin)
Filter by:

-   status
-   author
-   title
-   genre

Pagination (10 per page)
Automatic status changes when booked/returned

👤 Patrons
Full CRUD for patrons (admin only)
📘 Loans
Create a loan (admin)
Patron can only view their own loans
Automatic status updates for books
Overdue detection via Loan::overdue() scope

📊 Dashboard
Admin dashboard shows:

-   total books
-   available books
-   loaned books
-   total patrons
-   active loans
-   overdue loans

🌱 Database Seeders
Creates:
1 admin user
3 patrons
50 books
10 normal + 5 overdue loans

🧪 Tests
Complete feature test coverage:
Auth (login, register, logout)
Books CRUD + filtering + pagination
Patrons CRUD
Loans CRUD + business logic
Dashboard stats
Role permissions
API structure
All tests pass ➜ 100% green ✔

📦 Installation Instructions
1️⃣ Clone Repository & Install Dependencies
git clone https://github.com/ascargo/Sprint5.-lms_api.git
cd lms-api
composer install

2️⃣ Copy & Configure Environment
cp .env.example .env
php artisan key:generate

Update .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lms_api
DB_USERNAME=root
DB_PASSWORD=your_password

3️⃣ Run Migrations & Seeders
php artisan migrate:fresh --seed

This will generate:
Default Admin
email: admin@domus.com
password: password

Default Patrons
Auto-created (3 random users).

Books & Loans
~50 books, 10 loans, 5 overdue loans.

4️⃣ Start Development Server
php artisan serve
API now lives at:
👉 http://127.0.0.1:8000/api/v1

📘 API Documentation (Scribe)

After generating docs:
php artisan scribe:generate

View API docs at:
👉 http://127.0.0.1:8000/docs

Includes:
all endpoints
request/response examples
authentication instructions
Postman collection
OpenAPI/Swagger file

🧪 Running Tests
php artisan test

Expected result:
Tests: 35 passed
100% green ✔

🗂️ Project Structure (Important Folders)
app/
├── Http/
│ ├── Controllers/Api/V1/
│ ├── Middleware/
│ ├── Requests/
├── Models/
database/
├── factories/
├── migrations/
├── seeders/
tests/
├── Feature/
├── Unit/
routes/
├── api.php

🤝 Contribution Workflow

Follows Gitflow:
git checkout develop
git checkout -b feature/my-feature

# After coding:

git add .
git commit -m "feat: my feature"
git push origin feature/my-feature

Then open a Pull Request into develop.
