# 💰 Personal Finance Manager (Laravel)

A personal web application designed for straightforward financial management. It provides a clean interface to track daily expenses, monitor income, plan for future purchases, and digitally store receipts. The goal is to offer a simple, private, and efficient way to see where your money is going.

---

## 🚀 Features

- **Dashboard**: Overview of your financial health with charts and summaries
- **Transactions**: Log daily expenses and income with category tagging
- **Categories & Subcategories**: Organize spending into meaningful groups
- **Wishlist**: Track future purchases and savings goals
- **Reports**: Generate monthly and yearly financial summaries
- **User Authentication**: Secure login and registration
- **Settings**: Customize preferences and account details
- **Google Sheet Integration**: Sync data with your personal Google Sheets
- **Receipt Storage**: Upload and manage digital receipts

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12+
- **Frontend**: Blade / Vue.js / Bootstrap
- **Database**: MySQL
- **Icons**: Font Awesome
- **External Integration**: Google Sheets API

---

## 📦 Installation

```bash
git clone https://github.com/ljensomo/finance-core.git
cd finance-manager
composer install
cp .env.example .env
php artisan key:generate
