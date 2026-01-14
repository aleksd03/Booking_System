# ADBook - Resource Booking System
## Complete Technical Documentation

A comprehensive web-based resource booking system built with PHP and MySQL. The system allows users to browse and reserve various resources (conference rooms, sports facilities, equipment) while administrators can manage resources, reservations, and users.

## 📚 Table of Contents

1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Installation](#installation)
4. [Project Structure](#project-structure)
5. [Functionality](#functionality)
6. [Database](#database)

## ✨ Features

### User Features
- **Registration and Login**: Secure authentication system
- **Browse Resources**: View all available resources with category filtering
- **Make Reservations**: Book resources with automatic price calculation
- **My Reservations**: View and manage personal reservations (upcoming, past, cancelled)
- **Cancel Reservations**: Cancel upcoming reservations
- **Password Recovery**: Request password reset via admin contact

### Administrator Features
- **Dashboard**: Overview with statistics (total reservations, revenue, users, etc.)
- **Resource Management**: Full CRUD operations for resources (create, edit, delete)
- **Reservation Management**: View all reservations with advanced filtering
- **User Management**: View users, change roles, delete users
- **Real-time Statistics**: Track bookings, revenue, and system usage

### Technical Features
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Form Validation**: Client-side and server-side validation
- **Conflict Detection**: Prevents overlapping reservations
- **3-Hour Advance Notice**: Minimum 3-hour advance booking requirement
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
The database configuration is set in `config/database.php`:
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

## 👤 Setting Up Accounts

After running `database_reset.sql`, the database will be clean with no users.

### Creating Admin Account:
1. Go to: `http://localhost/booking-system`
2. Register a new account (use your preferred email and password)
3. In MySQL Workbench, run:
```sql
   UPDATE users SET role = 'admin' WHERE id = 1;
```
4. Logout and login again - you're now an admin!

### Creating Demo Users:
- Simply register additional accounts through the registration page
- They will automatically have the 'user' role
- Use these for testing reservations and user features

## 📂 Project Structure
```
booking-system/
├── admin/                      # Admin panel pages
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── resources.php          # Resource management
│   ├── resource_add.php       # Add new resource
│   ├── resource_edit.php      # Edit resource
│   ├── resource_delete.php    # Delete resource
│   ├── all_reservations.php   # View all reservations
│   ├── users.php              # User management
│   ├── user_change_role.php   # Change user role
│   └── user_delete.php        # Delete user
├── assets/
│   └── css/
│       └── style.css          # Main stylesheet
├── config/
│   └── database.php           # Database configuration
├── includes/
│   └── auth_check.php         # Authentication helper functions
├── pages/                      # User-facing pages
│   ├── login.php              # Login page
│   ├── login_process.php      # Login processing
│   ├── register.php           # Registration page
│   ├── register_process.php   # Registration processing
│   ├── reservations.php       # Browse resources
│   ├── create_reservation.php # Create new reservation
│   ├── create_reservation_process.php # Reservation processing
│   ├── my_reservations.php    # User's reservations
│   ├── cancel_reservation.php # Cancel reservation
│   ├── forgot_password.php    # Password recovery
│   └── logout.php             # Logout handler
├── index.php                   # Homepage
├── database_setup.sql          # SQL schema and sample data
├── README.md                   # Main documentation (this file)
├── DOCUMENTATION_BG.md         # Bulgarian documentation
└── DOCUMENTATION_EN.md         # English documentation (this file)
```

## 🗄️ Database

### Tables

#### 1. users (User Accounts)
Stores registered user information.
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR) - Full name
- email (VARCHAR, UNIQUE) - Email address
- phone (VARCHAR) - Phone number
- password (VARCHAR) - Hashed password
- role (ENUM: 'user', 'admin') - User role
- created_at (TIMESTAMP) - Registration date
```

#### 2. categories (Resource Categories)
Categories for different types of resources.
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR) - Category name
- icon (VARCHAR) - Emoji icon
- description (TEXT) - Description
```

**Sample Categories:**
- 🏢 Conference Rooms
- ⚽ Sports Facilities
- 💻 Equipment

#### 3. resources (Available Resources)
Resources available for booking.
```sql
- id (INT, PRIMARY KEY)
- category_id (INT, FOREIGN KEY)
- name (VARCHAR) - Resource name
- description (TEXT) - Description
- capacity (INT) - Capacity (people)
- price_per_hour (DECIMAL) - Price per hour in euros
- location (VARCHAR) - Location
- status (ENUM: 'available', 'unavailable') - Status
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

**Sample Resources:**
- Conference Room A (€45/hour)
- Conference Room B (€28/hour)
- Tennis Court 1 (€25/hour)
- Laptop Dell XPS (€8/hour)
- Projector Epson (€6/hour)
- Fitness Hall (€12/hour)

#### 4. reservations (Booking Records)
Records of made reservations.
```sql
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- resource_id (INT, FOREIGN KEY)
- start_datetime (DATETIME) - Reservation start
- end_datetime (DATETIME) - Reservation end
- total_price (DECIMAL) - Total price
- status (ENUM: 'pending', 'confirmed', 'cancelled', 'completed')
- notes (TEXT) - Additional notes
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Table Relationships
- **users** → **reservations** (one-to-many)
- **categories** → **resources** (one-to-many)
- **resources** → **reservations** (one-to-many)

## 🎨 Design

### Color Scheme
- **Primary Gradient**: Purple (#667eea) → Violet (#764ba2)
- **Success**: Green (#28a745)
- **Error**: Red (#dc3545)
- **Warning**: Yellow (#ffc107)
- **Info**: Blue (#3498db)

### Responsive Design
- **Desktop**: Full functionality with multi-column layout
- **Tablet**: Adapted layout with 2 columns
- **Mobile**: Single-column layout with optimized navigation

### UI Components
- Resource cards with hover effects
- Status badges with color coding
- Interactive buttons
- Forms with real-time validation
- Tables with sorting and filtering

## 🔒 Security

### Implemented Measures
1. **Password Hashing**: Uses `password_hash()` and `password_verify()`
2. **SQL Injection Protection**: Prepared statements for all queries
3. **XSS Protection**: `htmlspecialchars()` for all output data
4. **Session Security**: Secure session management
5. **Role-Based Access**: Different permissions for users and admins
6. **Input Validation**: Client-side and server-side validation
7. **CSRF Protection**: Session-based verification

## 📊 Business Logic

### Reservation Rules
1. **Minimum Duration**: 30 minutes
2. **Maximum Duration**: 24 hours
3. **Advance Notice**: Minimum 3 hours before start time
4. **Conflicts**: System prevents overlapping reservations
5. **Pricing**: Automatic calculation: `hours × price_per_hour`

### Reservation Statuses
- **pending**: Awaiting confirmation
- **confirmed**: Active reservation
- **cancelled**: Cancelled by user
- **completed**: Past reservation

## 🎯 Functional Requirements

### Users Can:
- ✅ Register an account with name, email, phone, and password
- ✅ Log into the system
- ✅ View available resources
- ✅ Filter resources by categories
- ✅ Create reservations by selecting date, time, and duration
- ✅ See automatically calculated price before confirming
- ✅ View their reservations (upcoming, past, cancelled)
- ✅ Cancel upcoming reservations
- ✅ Log out of the system

### Administrators Can:
- ✅ View dashboard with statistics
- ✅ Manage resources (add, edit, delete)
- ✅ View all reservations with filters
- ✅ Manage users (change roles, delete)
- ✅ View total revenue and statistics
- ✅ View recent reservations

## 🧪 Testing

### Test Scenarios

#### 1. Registration and Login
- ✅ Registration with valid data
- ✅ Registration with existing email (error)
- ✅ Login with correct credentials
- ✅ Login with incorrect credentials (error)

#### 2. Reservations
- ✅ Create valid reservation
- ✅ Attempt to book in the past (error)
- ✅ Attempt overlapping reservation (error)
- ✅ Cancel upcoming reservation
- ✅ Attempt to cancel past reservation (error)

#### 3. Admin Panel
- ✅ Add new resource
- ✅ Edit existing resource
- ✅ Delete resource
- ✅ Change user role
- ✅ Delete user

## 📱 Supported Browsers

- ✅ Google Chrome (latest)
- ✅ Mozilla Firefox (latest)
- ✅ Safari (latest)
- ✅ Microsoft Edge (latest)

## 🚫 Known Limitations

- Email notifications are simulated (no actual email sending)
- Single currency support (Euro)
- No payment gateway integration
- Basic search functionality

## 📈 Future Improvements (Optional)

- 📧 Real email notifications
- 💳 Payment gateway integration (Stripe/PayPal)
- 🔍 Advanced search functionality
- 📅 Calendar view for reservations
- 📊 Statistics export (PDF/Excel)
- 🌐 Multi-language support
- 📱 Mobile application

## 👨‍💻 Author

**Aleks Dimitrov**
- University Project - Scripting languages ​​for the Internet (PHP)
- January 2026
- GitHub: [github.com/aleksd03](https://github.com/aleksd03)

---

**Note**: This is a student project created for educational purposes as part of a web development course.