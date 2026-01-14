<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Bulgarian day names
$days_bg = [
    'Monday' => 'Понеделник',
    'Tuesday' => 'Вторник',
    'Wednesday' => 'Сряда',
    'Thursday' => 'Четвъртък',
    'Friday' => 'Петък',
    'Saturday' => 'Събота',
    'Sunday' => 'Неделя'
];

// Get filter
$filter = $_GET['filter'] ?? 'all';

// Build query based on filter
$where_clause = '';
$now = date('Y-m-d H:i:s');

switch ($filter) {
    case 'upcoming':
        $where_clause = "WHERE r.start_datetime > '$now' AND r.status IN ('pending', 'confirmed')";
        break;
    case 'today':
        $today = date('Y-m-d');
        $where_clause = "WHERE DATE(r.start_datetime) = '$today' AND r.status IN ('pending', 'confirmed')";
        break;
    case 'past':
        $where_clause = "WHERE r.start_datetime < '$now'";
        break;
    case 'cancelled':
        $where_clause = "WHERE r.status = 'cancelled'";
        break;
    case 'confirmed':
        $where_clause = "WHERE r.status = 'confirmed'";
        break;
    default:
        $where_clause = '';
}

// Get all reservations
$conn = getDbConnection();
$query = "SELECT r.*, res.name as resource_name, u.name as user_name, u.email as user_email,
          c.name as category_name, c.icon as category_icon
          FROM reservations r
          JOIN resources res ON r.resource_id = res.id
          JOIN users u ON r.user_id = u.id
          JOIN categories c ON res.category_id = c.id
          $where_clause
          ORDER BY r.start_datetime DESC";

$reservations = $conn->query($query);

// Get counts for filters
$total_count = $conn->query("SELECT COUNT(*) as cnt FROM reservations")->fetch_assoc()['cnt'];
$upcoming_count = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE start_datetime > NOW() AND status IN ('pending', 'confirmed')")->fetch_assoc()['cnt'];
$today_count = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE DATE(start_datetime) = CURDATE() AND status IN ('pending', 'confirmed')")->fetch_assoc()['cnt'];
$cancelled_count = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE status = 'cancelled'")->fetch_assoc()['cnt'];
$confirmed_count = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE status = 'confirmed'")->fetch_assoc()['cnt'];

closeDbConnection($conn);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Всички резервации - ADBook</title>
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
        <h2 class="page-title">Всички резервации</h2>

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

        <!-- Filters -->
        <div class="filter-bar">
            <a href="all_reservations.php?filter=all" 
               class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-filter'; ?>">
                Всички (<?php echo $total_count; ?>)
            </a>
            <a href="all_reservations.php?filter=upcoming" 
               class="btn <?php echo $filter === 'upcoming' ? 'btn-primary' : 'btn-filter'; ?>">
                📅 Предстоящи (<?php echo $upcoming_count; ?>)
            </a>
            <a href="all_reservations.php?filter=today" 
               class="btn <?php echo $filter === 'today' ? 'btn-primary' : 'btn-filter'; ?>">
                🗓️ Днес (<?php echo $today_count; ?>)
            </a>
            <a href="all_reservations.php?filter=confirmed" 
               class="btn <?php echo $filter === 'confirmed' ? 'btn-primary' : 'btn-filter'; ?>">
                ✅ Потвърдени (<?php echo $confirmed_count; ?>)
            </a>
            <a href="all_reservations.php?filter=cancelled" 
               class="btn <?php echo $filter === 'cancelled' ? 'btn-primary' : 'btn-filter'; ?>">
                ❌ Отказани (<?php echo $cancelled_count; ?>)
            </a>
        </div>

        <!-- Reservations Table -->
        <div class="table-container">
            <?php if ($reservations->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Потребител</th>
                            <th>Ресурс</th>
                            <th>Категория</th>
                            <th>Дата</th>
                            <th>Време</th>
                            <th>Продължителност</th>
                            <th>Цена</th>
                            <th>Статус</th>
                            <th>Бележки</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($res = $reservations->fetch_assoc()): ?>
                            <?php
                            $start = new DateTime($res['start_datetime']);
                            $end = new DateTime($res['end_datetime']);
                            $duration = $start->diff($end);
                            $total_minutes = ($duration->h * 60) + $duration->i + ($duration->days * 24 * 60);
                            ?>
                            <tr>
                                <td><strong>#<?php echo $res['id']; ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($res['user_name']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($res['user_email']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res['category_icon']); ?> <?php echo htmlspecialchars($res['resource_name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($res['category_name']); ?></td>
                                <td>
                                    <?php echo $start->format('d.m.Y'); ?>
                                    <br>
                                    <small class="text-muted"><?php echo $days_bg[$start->format('l')]; ?></small>
                                </td>
                                <td><?php echo $start->format('H:i'); ?> - <?php echo $end->format('H:i'); ?></td>
                                <td>
                                    <?php 
                                    if ($total_minutes >= 60) {
                                        $display_hours = floor($total_minutes / 60);
                                        $display_minutes = $total_minutes % 60;
                                        echo $display_hours . ' час' . ($display_hours == 1 ? '' : 'а');
                                        if ($display_minutes > 0) {
                                            echo ' и ' . $display_minutes . ' мин';
                                        }
                                    } else {
                                        echo $total_minutes . ' мин';
                                    }
                                    ?>
                                </td>
                                <td><strong><?php echo number_format($res['total_price'], 2); ?> €</strong></td>
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
                                    <?php 
                                    if (!empty($res['notes'])) {
                                        echo '<small>' . htmlspecialchars(substr($res['notes'], 0, 30));
                                        if (strlen($res['notes']) > 30) echo '...';
                                        echo '</small>';
                                    } else {
                                        echo '<small class="text-muted">-</small>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center-muted">Няма резервации за показване</p>
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