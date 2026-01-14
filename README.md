# ADBook - Resource Booking System

A comprehensive web-based resource booking system built with PHP and MySQL. The system allows users to browse and reserve various resources (conference rooms, sports facilities, equipment) while administrators can manage resources, reservations, and users.

## 🚀 Features

### User Features
- **User Authentication**: Secure registration and login system
- **Browse Resources**: View all available resources with filtering by category
- **Make Reservations**: Book resources with automatic price calculation
- **My Reservations**: View and manage personal reservations (upcoming, past, cancelled)
- **Cancel Reservations**: Cancel upcoming reservations
- **Password Recovery**: Request password reset via admin contact

### Admin Features
- **Dashboard**: Overview with statistics (total reservations, revenue, users, etc.)
- **Resource Management**: Full CRUD operations for resources
- **Reservation Management**: View all reservations with advanced filtering
- **User Management**: View users, change roles, delete users
- **Real-time Statistics**: Track bookings, revenue, and system usage

### Technical Features
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Form Validation**: Client-side and server-side validation
- **Conflict Detection**: Prevents overlapping reservations
- **3-Hour Advance Booking**: Enforces minimum 3-hour advance notice
- **Session Management**: Secure user sessions with role-based access
- **Clean Code**: No inline styles, proper MVC-like structure

## 🛠️ Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Server**: Apache (XAMPP)
- **Version Control**: Git & GitHub

## 📋 Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

## 🔧 Installation

### 1. Install XAMPP
Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)

### 2. Clone the Repository
```bash
cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/booking-system.git
```

### 3. Create Database
1. Start XAMPP (Apache and MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create a new database named `booking_system`
4. Import the SQL file: `database_setup.sql`

### 4. Configure Database Connection
The database configuration is already set in `config/database.php`:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booking_system';
```

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/booking-system
```

## 👤 Default Accounts

### Admin Account
- **Email**: admin@booking.com
- **Password**: admin123

### Demo User Account
- **Email**: demo@test.com
- **Password**: demo123

## 📂 Project Structure
```
booking-system/
├── admin/                      # Admin panel pages
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── resources.php          # Resource management
│   ├── all_reservations.php  # All reservations view
│   └── users.php              # User management
├── assets/
│   └── css/
│       └── style.css          # Main stylesheet
├── config/
│   └── database.php           # Database configuration
├── includes/
│   └── auth_check.php         # Authentication helper functions
├── pages/                      # User-facing pages
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   ├── reservations.php       # Browse resources
│   ├── create_reservation.php # Create new reservation
│   ├── my_reservations.php    # User's reservations
│   └── logout.php             # Logout handler
├── index.php                   # Homepage
├── database_setup.sql          # Database schema and sample data
└── README.md                   # This file
```

## 🗄️ Database Schema

### Tables
- **users**: User accounts and authentication
- **categories**: Resource categories (Conference Rooms, Sports, Equipment)
- **resources**: Available resources for booking
- **reservations**: Booking records

### Key Relationships
- Users → Reservations (one-to-many)
- Categories → Resources (one-to-many)
- Resources → Reservations (one-to-many)

## 🎨 Design Features

- Modern gradient UI with purple/blue theme
- Card-based layout for resources
- Responsive grid system
- Status badges with color coding
- Interactive buttons with hover effects
- Clean typography and spacing

## 🔒 Security Features

- Password hashing with `password_hash()` and `password_verify()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- Role-based access control (user/admin)
- Input validation (client-side and server-side)

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 🐛 Known Limitations

- Email notifications are simulated (no actual email sending)
- Single currency support (Euro)
- No payment gateway integration
- Basic search functionality

## 🤝 Contributing

This is a university project. Contributions are not currently accepted.

## 📄 License

This project is for educational purposes only.

## 👨‍💻 Author

**Aleks Dimitrov**
- University Project
- Date: January 2026

---

**Note**: This is a student project created for educational purposes as part of a web development course.