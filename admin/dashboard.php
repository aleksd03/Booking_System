<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get statistics
$conn = getDbConnection();

// Total resources
$result = $conn->query("SELECT COUNT(*) as total FROM resources WHERE status = 'available'");
$total_resources = $result->fetch_assoc()['total'];

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_users = $result->fetch_assoc()['total'];

// Total reservations
$result = $conn->query("SELECT COUNT(*) as total FROM reservations");
$total_reservations = $result->fetch_assoc()['total'];

// Upcoming reservations
$result = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE start_datetime > NOW() AND status IN ('pending', 'confirmed')");
$upcoming_reservations = $result->fetch_assoc()['total'];

// Today's reservations
$result = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE DATE(start_datetime) = CURDATE() AND status IN ('pending', 'confirmed')");
$today_reservations = $result->fetch_assoc()['total'];

// Total revenue (confirmed reservations)
$result = $conn->query("SELECT SUM(total_price) as revenue FROM reservations WHERE status = 'confirmed'");
$total_revenue = $result->fetch_assoc()['revenue'] ?? 0;

// Recent reservations
$recent_query = "SELECT r.*, res.name as resource_name, u.name as user_name, c.name as category_name
                 FROM reservations r
                 JOIN resources res ON r.resource_id = res.id
                 JOIN users u ON r.user_id = u.id
                 JOIN categories c ON res.category_id = c.id
                 ORDER BY r.created_at DESC
                 LIMIT 5";
$recent_reservations = $conn->query($recent_query);

closeDbConnection($conn);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панел - ADBook</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="../index.php" class="logo-link"><h1>ADBook - Админ</h1></a>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="resources.php">Ресурси</a></li>
                    <li><a href="all_reservations.php">Резервации</a></li>
                    <li><a href="users.php">Потребители</a></li>
                    <li><a href="../index.php">← Към сайта</a></li>
                    <li><a href="../pages/logout.php">Изход (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <h2 class="page-title">Админ Dashboard</h2>

        <?php
        // Display messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <div class="stat-card stat-card-blue">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <h3><?php echo $total_reservations; ?></h3>
                    <p>Общо резервации</p>
                </div>
            </div>

            <div class="stat-card stat-card-green">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3><?php echo $upcoming_reservations; ?></h3>
                    <p>Предстоящи резервации</p>
                </div>
            </div>

            <div class="stat-card stat-card-orange">
                <div class="stat-icon">🗓️</div>
                <div class="stat-content">
                    <h3><?php echo $today_reservations; ?></h3>
                    <p>Резервации днес</p>
                </div>
            </div>

            <div class="stat-card stat-card-purple">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3><?php echo number_format($total_revenue, 2); ?> €</h3>
                    <p>Общи приходи</p>
                </div>
            </div>

            <div class="stat-card stat-card-teal">
                <div class="stat-icon">🏢</div>
                <div class="stat-content">
                    <h3><?php echo $total_resources; ?></h3>
                    <p>Налични ресурси</p>
                </div>
            </div>

            <div class="stat-card stat-card-red">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Регистрирани потребители</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-section">
            <h3 class="admin-section-title">Бързи действия</h3>
            <div class="admin-actions">
                <a href="resource_add.php" class="admin-action-btn">
                    ➕ Добави нов ресурс
                </a>
                <a href="all_reservations.php" class="admin-action-btn">
                    📋 Виж всички резервации
                </a>
                <a href="users.php" class="admin-action-btn">
                    👥 Управление на потребители
                </a>
                <a href="resources.php" class="admin-action-btn">
                    🏢 Управление на ресурси
                </a>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="table-container">
            <h3 class="admin-section-title">Последни резервации</h3>
            <?php if ($recent_reservations->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Потребител</th>
                            <th>Ресурс</th>
                            <th>Категория</th>
                            <th>Дата и час</th>
                            <th>Цена</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = $recent_reservations->fetch_assoc()): ?>
                            <?php
                            $start = new DateTime($res['start_datetime']);
                            ?>
                            <tr>
                                <td>#<?php echo $res['id']; ?></td>
                                <td><?php echo htmlspecialchars($res['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['resource_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['category_name']); ?></td>
                                <td><?php echo $start->format('d.m.Y H:i'); ?></td>
                                <td><?php echo number_format($res['total_price'], 2); ?> €</td>
                                <td>
                                    <span class="badge badge-<?php echo $res['status']; ?>">
                                        <?php
                                        $status_labels = [
                                            'pending' => 'В очакване',
                                            'confirmed' => 'Потвърдена',
                                            'cancelled' => 'Отказана',
                                            'completed' => 'Завършена'
                                        ];
                                        echo $status_labels[$res['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="all_reservations.php" class="text-link">Виж детайли</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center-muted">Няма резервации</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>