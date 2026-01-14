# 📅 ADBook - Resource Reservation Platform

## 📖 Description / Описание

**EN:** ADBook is a comprehensive resource booking system for managing conference rooms, sports facilities, and equipment reservations. Built with pure PHP (no frameworks), MySQL, HTML5, CSS3, and JavaScript.

**BG:** ADBook е система за резервация на ресурси (конферентни зали, спортни съоръжения, оборудване) разработена с PHP, MySQL, HTML, CSS и JavaScript.

## 📁 Project Structure / Структура на проекта

```
booking-system/
├── config/              # Конфигурационни файлове
│   └── database.php     # Настройки за база данни
├── includes/            # Общи PHP файлове
├── assets/              # Статични ресурси
│   ├── css/
│   │   └── style.css    # Основен CSS файл
│   ├── js/
│   │   └── main.js      # Основен JavaScript файл
│   └── images/          # Изображения
├── pages/               # Страници на приложението
│   ├── login.php        # Страница за вход
│   ├── register.php     # Страница за регистрация
│   └── reservations.php # Страница с резервации
├── admin/               # Административен панел
└── index.php            # Главна страница
```

## ✨ Features / Функционалности

### Completed / Готово:
- ✅ User registration and authentication
- ✅ Responsive modern design
- ✅ MySQL database with relational structure
- ✅ Resource categorization
- ✅ Sample data included

### Planned / Планирано:
- ⏳ View available resources
- ⏳ Create, edit, and delete reservations
- ⏳ Admin panel for resource management
- ⏳ Reservation history
- ⏳ Search and filtering
- ⏳ Calendar view

## 🚀 Installation and Setup / Инсталация и настройка

### Requirements / Изисквания:
- XAMPP (Apache + MySQL + PHP 7.4+)
- Modern web browser (Chrome, Firefox, Edge)
- Git (optional, for version control)

### Installation Steps / Стъпки за инсталация:

1. **Install XAMPP / Инсталирайте XAMPP**
   ```
   Download from: https://www.apachefriends.org/
   ```

2. **Clone or copy the project / Клонирайте или копирайте проекта**
   ```bash
   # If using Git
   git clone <your-repo-url>
   
   # Or copy the folder to:
   # C:\xampp\htdocs\booking-system\
   ```

3. **Start Apache and MySQL / Стартирайте Apache и MySQL**
   - Open XAMPP Control Panel
   - Click "Start" on Apache
   - Click "Start" on MySQL

4. **Create the database / Създайте базата данни**
   - Open browser: `http://localhost/phpmyadmin`
   - Create new database: `booking_system`
   - Import `database_setup.sql` or run it in MySQL Workbench

5. **Configure database connection / Конфигурирайте връзката с БД**
   - Edit `config/database.php` if needed
   - Default settings:
     - Host: `localhost`
     - User: `root`
     - Password: `` (empty)
     - Database: `booking_system`

6. **Open the project / Отворете проекта**
   ```
   http://localhost/booking-system/
   ```

## 🔐 Default Admin Credentials / Данни за администратор

After running the database setup, you can login with:
```
Email: admin@booking.com
Password: admin123
```

**⚠️ Important:** Change the admin password after first login!

## 🛠️ Technologies / Технологии

- **Backend:** PHP (Pure PHP, no frameworks)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Server:** Apache (XAMPP)

## 📸 Screenshots / Снимки

_Coming soon / Очаквайте скоро_

## 📦 Database Schema / Структура на базата данни

### Tables / Таблици:
- **users** - User accounts with roles
- **categories** - Resource categories
- **resources** - Bookable resources
- **reservations** - User reservations

## 🤝 Contributing / Принос

This is a university project. Suggestions and feedback are welcome!

## 📄 License / Лиценз

This project is created for educational purposes.

## 👨‍💻 Author / Автор

**ADBook** - University Project - Web Development with PHP  
Created by aleksd03

---

**Note:** This project is currently in development. More features coming soon!

**Забележка:** Проектът е в процес на разработка. Очаквайте нови функционалности скоро!
