<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Check if user is logged in
requireLogin();

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

$user_id = getUserId();

// Get all reservations for current user
$conn = getDbConnection();
$query = "SELECT r.*, res.name as resource_name, res.description as resource_description, 
          c.name as category_name, c.icon as category_icon
          FROM reservations r
          JOIN resources res ON r.resource_id = res.id
          JOIN categories c ON res.category_id = c.id
          WHERE r.user_id = ?
          ORDER BY r.start_datetime DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

$stmt->close();
closeDbConnection($conn);

// Separate reservations by status
$upcoming = [];
$past = [];
$cancelled = [];

$now = new DateTime();

foreach ($reservations as $reservation) {
    $start_date = new DateTime($reservation['start_datetime']);
    
    if ($reservation['status'] === 'cancelled') {
        $cancelled[] = $reservation;
    } elseif ($start_date > $now) {
        $upcoming[] = $reservation;
    } else {
        $past[] = $reservation;
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моите резервации - ADBook</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="../index.php" class="logo-link"><h1>ADBook</h1></a>
                <ul class="nav-menu">
                    <li><a href="../index.php">Начало</a></li>
                    <li><a href="reservations.php">Резервации</a></li>
                    <li><a href="my_reservations.php">Моите резервации</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="../admin/dashboard.php">Админ панел</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Изход (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <h2 class="page-title">Моите резервации</h2>
        
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

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="showTab('upcoming')">
                Предстоящи (<?php echo count($upcoming); ?>)
            </button>
            <button class="tab-btn" onclick="showTab('past')">
                Минали (<?php echo count($past); ?>)
            </button>
            <button class="tab-btn" onclick="showTab('cancelled')">
                Отказани (<?php echo count($cancelled); ?>)
            </button>
        </div>

        <!-- Upcoming Reservations -->
        <div id="upcoming-tab" class="tab-content">
            <?php if (empty($upcoming)): ?>
                <div class="alert alert-error alert-center">
                    <p>Нямате предстоящи резервации.</p>
                    <a href="reservations.php" class="btn btn-primary btn-margin-top">Направете резервация</a>
                </div>
            <?php else: ?>
                <div class="reservations-list">
                    <?php foreach ($upcoming as $reservation): ?>
                        <?php
                        $start = new DateTime($reservation['start_datetime']);
                        $end = new DateTime($reservation['end_datetime']);
                        $duration = $start->diff($end);
                        $hours = $duration->h + ($duration->days * 24);
                        ?>
                        <div class="reservation-card">
                            <div class="reservation-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($reservation['category_icon']); ?> <?php echo htmlspecialchars($reservation['resource_name']); ?></h3>
                                    <span class="reservation-category"><?php echo htmlspecialchars($reservation['category_name']); ?></span>
                                </div>
                                <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                    <?php
                                    $status_labels = [
                                        'pending' => 'В очакване',
                                        'confirmed' => 'Потвърдена',
                                        'cancelled' => 'Отказана',
                                        'completed' => 'Завършена'
                                    ];
                                    echo $status_labels[$reservation['status']];
                                    ?>
                                </span>
                            </div>
                            
                            <div class="reservation-body">
                                <div class="reservation-details">
                                    <div class="detail-row">
                                        <strong>📅 Дата:</strong> 
                                        <?php echo $start->format('d.m.Y') . ' (' . $days_bg[$start->format('l')] . ')'; ?>
                                    </div>
                                    <div class="detail-row">
                                        <strong>🕐 Време:</strong> 
                                        <?php echo $start->format('H:i'); ?> - <?php echo $end->format('H:i'); ?> 
                                        (<?php echo $hours; ?> час<?php echo $hours == 1 ? '' : 'а'; ?>)
                                    </div>
                                    <div class="detail-row">
                                        <strong>💰 Цена:</strong> 
                                        <?php echo number_format($reservation['total_price'], 2); ?> лв
                                    </div>
                                    <?php if (!empty($reservation['notes'])): ?>
                                        <div class="detail-row">
                                            <strong>📝 Бележки:</strong> 
                                            <?php echo htmlspecialchars($reservation['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="reservation-footer">
                                <?php if ($reservation['status'] !== 'cancelled'): ?>
                                    <button onclick="cancelReservation(<?php echo $reservation['id']; ?>)" class="btn btn-danger">
                                        ❌ Откажи резервация
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Past Reservations -->
        <div id="past-tab" class="tab-content tab-hidden">
            <?php if (empty($past)): ?>
                <div class="alert alert-error alert-center">
                    Няма минали резервации.
                </div>
            <?php else: ?>
                <div class="reservations-list">
                    <?php foreach ($past as $reservation): ?>
                        <?php
                        $start = new DateTime($reservation['start_datetime']);
                        $end = new DateTime($reservation['end_datetime']);
                        $duration = $start->diff($end);
                        $hours = $duration->h + ($duration->days * 24);
                        ?>
                        <div class="reservation-card reservation-card-past">
                            <div class="reservation-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($reservation['category_icon']); ?> <?php echo htmlspecialchars($reservation['resource_name']); ?></h3>
                                    <span class="reservation-category"><?php echo htmlspecialchars($reservation['category_name']); ?></span>
                                </div>
                                <span class="status-badge status-completed">Завършена</span>
                            </div>
                            
                            <div class="reservation-body">
                                <div class="reservation-details">
                                    <div class="detail-row">
                                        <strong>📅 Дата:</strong> 
                                        <?php echo $start->format('d.m.Y') . ' (' . $days_bg[$start->format('l')] . ')'; ?>
                                    </div>
                                    <div class="detail-row">
                                        <strong>🕐 Време:</strong> 
                                        <?php echo $start->format('H:i'); ?> - <?php echo $end->format('H:i'); ?>
                                    </div>
                                    <div class="detail-row">
                                        <strong>💰 Цена:</strong> 
                                        <?php echo number_format($reservation['total_price'], 2); ?> лв
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cancelled Reservations -->
        <div id="cancelled-tab" class="tab-content tab-hidden">
            <?php if (empty($cancelled)): ?>
                <div class="alert alert-error alert-center">
                    Няма отказани резервации.
                </div>
            <?php else: ?>
                <div class="reservations-list">
                    <?php foreach ($cancelled as $reservation): ?>
                        <?php
                        $start = new DateTime($reservation['start_datetime']);
                        $end = new DateTime($reservation['end_datetime']);
                        ?>
                        <div class="reservation-card reservation-card-cancelled">
                            <div class="reservation-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($reservation['category_icon']); ?> <?php echo htmlspecialchars($reservation['resource_name']); ?></h3>
                                    <span class="reservation-category"><?php echo htmlspecialchars($reservation['category_name']); ?></span>
                                </div>
                                <span class="status-badge status-cancelled">Отказана</span>
                            </div>
                            
                            <div class="reservation-body">
                                <div class="reservation-details">
                                    <div class="detail-row">
                                        <strong>📅 Дата:</strong> 
                                        <?php echo $start->format('d.m.Y') . ' (' . $days_bg[$start->format('l')] . ')'; ?>
                                    </div>
                                    <div class="detail-row">
                                        <strong>🕐 Време:</strong> 
                                        <?php echo $start->format('H:i'); ?> - <?php echo $end->format('H:i'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>

    <script>
        // Tab switching
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('tab-hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('tab-hidden');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Cancel reservation
        function cancelReservation(reservationId) {
            if (confirm('Сигурни ли сте, че искате да откажете тази резервация?')) {
                window.location.href = 'cancel_reservation.php?id=' + reservationId;
            }
        }
    </script>
</body>
</html>