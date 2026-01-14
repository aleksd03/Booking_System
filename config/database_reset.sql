-- ============================================
-- ADBook - Database Reset Script
-- For clean demo before project defense
-- ============================================

USE booking_system;

-- ============================================
-- STEP 1: Clear ALL data (including users)
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE reservations;
TRUNCATE TABLE users;
-- Resources and categories remain with updated prices

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- DONE! Database is now clean
-- ============================================

/*
NEXT STEPS:
1. Go to: http://localhost/booking-system
2. Register a new account (this will be your admin account)
3. Register another account (this will be demo user)
4. Run this SQL to make first user admin:
   UPDATE users SET role = 'admin' WHERE id = 1;

5. Now you have:
   - User #1: Admin (your account)
   - User #2: Demo user (test account)

6. Make some test reservations if needed
*/

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check users (should be empty initially)
SELECT id, name, email, role FROM users;

-- Check resources (should show 6 with Euro prices)
SELECT id, name, price_per_hour, status FROM resources;

-- Check reservations (should be empty)
SELECT COUNT(*) as total_reservations FROM reservations;