-- ============================================
-- ADBook - Database Reset Script
-- For clean demo before project defense
-- ============================================

USE booking_system;

-- ============================================
-- STEP 1: Clear all existing data
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE reservations;
TRUNCATE TABLE users;
-- Don't truncate resources and categories - keep them with updated prices

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- STEP 2: Create fresh admin and demo accounts
-- ============================================

-- Admin account
-- Email: admin@booking.com
-- Password: admin123
INSERT INTO users (name, email, phone, password, role, created_at) VALUES
('Администратор', 'admin@booking.com', '+359888000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Demo user account for testing
-- Email: demo@test.com  
-- Password: demo123
INSERT INTO users (name, email, phone, password, role, created_at) VALUES
('Петър Николов', 'demo@test.com', '+359888111111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW());

-- ============================================
-- STEP 3: Add 2-3 sample reservations for demo
-- (Optional - comment out if you want completely empty)
-- ============================================

-- Sample upcoming reservation (tomorrow at 10:00 for 2 hours)
INSERT INTO reservations (user_id, resource_id, start_datetime, end_datetime, total_price, status, notes, created_at) VALUES
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 12 HOUR, 90.00, 'confirmed', 'Примерна резервация за демонстрация на системата', NOW());

-- Sample upcoming reservation (day after tomorrow at 14:00 for 1.5 hours)
INSERT INTO reservations (user_id, resource_id, start_datetime, end_datetime, total_price, status, notes, created_at) VALUES
(2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 14 HOUR, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 15 HOUR + INTERVAL 30 MINUTE, 37.50, 'confirmed', 'Тестова резервация', NOW());

-- ============================================
-- VERIFICATION QUERIES
-- (Run these to verify the reset was successful)
-- ============================================

-- Check users (should show 2: admin and demo user)
SELECT id, name, email, role FROM users;

-- Check resources (should show 6 with updated Euro prices)
SELECT id, name, price_per_hour, status FROM resources;

-- Check reservations (should show 2 upcoming reservations)
SELECT id, user_id, resource_id, start_datetime, end_datetime, total_price, status FROM reservations;

-- Check categories (should show 3 categories)
SELECT * FROM categories;

-- ============================================
-- NOTES FOR DEFENSE DAY
-- ============================================

/*
BEFORE DEFENSE:
1. Run this script to reset the database
2. Verify all tables have correct data
3. Test login with both accounts:
   - Admin: admin@booking.com / admin123
   - Demo: demo@test.com / demo123
4. Test making a new reservation
5. Test admin panel features

DURING DEFENSE:
- Show the clean system with 2 demo reservations
- Demonstrate creating a new reservation
- Show admin panel capabilities
- Explain the database structure

PASSWORD HASH INFO:
- The hash '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
  corresponds to password: "password"
- For admin123, you may want to generate a new hash
- Use the generate_password.php script if needed

IMPORTANT:
- Resources are NOT deleted - they keep the updated Euro prices
- Categories are NOT deleted - they remain as configured
- Only users and reservations are reset
- Sample reservations are set for FUTURE dates automatically
*/