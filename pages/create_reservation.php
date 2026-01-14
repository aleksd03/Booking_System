<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Check if user is logged in
requireLogin();

// Get resource ID
if (!isset($_GET['resource_id']) || empty($_GET['resource_id'])) {
    $_SESSION['error_message'] = "Невалиден ресурс";
    header('Location: reservations.php');
    exit();
}

$resource_id = intval($_GET['resource_id']);

// Get resource details
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon 
                        FROM resources r 
                        JOIN categories c ON r.category_id = c.id 
                        WHERE r.id = ? AND r.status = 'available'");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Ресурсът не е наличен";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: reservations.php');
    exit();
}

$resource = $result->fetch_assoc();
$stmt->close();
closeDbConnection($conn);

// Get old form data if validation failed
$old_data = $_SESSION['reservation_data'] ?? [];
unset($_SESSION['reservation_data']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Резервация - <?php echo htmlspecialchars($resource['name']); ?> - ADBook</title>
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
        <div class="form-container form-container-small">
            <h2 class="reservation-page-title">Нова резервация</h2>
            
            <?php
            // Display errors if any
            if (isset($_SESSION['reservation_errors'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['reservation_errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['reservation_errors']);
            }
            ?>

            <!-- Resource Info -->
            <div class="reservation-resource-info">
                <h3 class="resource-info-title">
                    <?php echo htmlspecialchars($resource['category_icon']); ?> 
                    <?php echo htmlspecialchars($resource['name']); ?>
                </h3>
                <p class="resource-info-subtitle"><?php echo htmlspecialchars($resource['description']); ?></p>
                
                <div class="resource-info-box">
                    <?php if ($resource['capacity']): ?>
                        <p><strong>👥 Капацитет:</strong> 
                        <?php 
                            $capacity = $resource['capacity'];
                            echo htmlspecialchars($capacity) . ' ' . ($capacity == 1 ? 'човек' : 'човека');
                        ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($resource['location']): ?>
                        <p><strong>📍 Локация:</strong> <?php echo htmlspecialchars($resource['location']); ?></p>
                    <?php endif; ?>
                    
                    <p class="price-highlight">
                        <strong>💰 Цена:</strong> <?php echo number_format($resource['price_per_hour'], 2); ?> лв/час
                    </p>
                </div>
            </div>

            <!-- Reservation Form -->
            <form action="create_reservation_process.php" method="POST" id="reservationForm">
                <input type="hidden" name="resource_id" value="<?php echo $resource['id']; ?>">
                
                <div class="form-group">
                    <label for="reservation_date">Дата на резервация:</label>
                    <input type="date" id="reservation_date" name="reservation_date" required
                           min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo htmlspecialchars($old_data['reservation_date'] ?? date('Y-m-d')); ?>">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="start_time">Начален час:</label>
                        <input type="time" id="start_time" name="start_time" required
                               step="900"
                               value="<?php echo htmlspecialchars($old_data['start_time'] ?? '09:00'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_time">Краен час:</label>
                        <input type="time" id="end_time" name="end_time" required
                               step="900"
                               value="<?php echo htmlspecialchars($old_data['end_time'] ?? '10:00'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Бележки (опционално):</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Добавете допълнителна информация за вашата резервация..."><?php echo htmlspecialchars($old_data['notes'] ?? ''); ?></textarea>
                </div>

                <!-- Price Calculation -->
                <div id="priceCalculation" class="price-calculation-box">
                    <p>
                        <strong>Продължителност:</strong> <span id="duration">-</span>
                    </p>
                    <p class="price-total">
                        <strong>Обща цена:</strong> <span id="totalPrice">0.00</span> лв
                    </p>
                </div>

                <button type="submit" class="btn btn-primary btn-full-width">
                    ✅ Потвърди резервация
                </button>
                
                <a href="reservations.php" class="btn btn-back">
                    ← Назад към ресурси
                </a>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>

    <script>
        // Price calculation
        const pricePerHour = <?php echo $resource['price_per_hour']; ?>;
        const dateInput = document.getElementById('reservation_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const priceCalculation = document.getElementById('priceCalculation');
        const durationSpan = document.getElementById('duration');
        const totalPriceSpan = document.getElementById('totalPrice');

        function calculatePrice() {
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (startTime && endTime) {
                const start = new Date('2000-01-01 ' + startTime);
                const end = new Date('2000-01-01 ' + endTime);
                
                const diffMs = end - start;
                const diffHours = diffMs / (1000 * 60 * 60);
                const diffMinutes = diffMs / (1000 * 60);
                
                if (diffHours > 0) {
                    const totalPrice = diffHours * pricePerHour;
                    
                    // Display duration
                    if (diffHours >= 1) {
                        const hours = Math.floor(diffHours);
                        const minutes = Math.round((diffHours - hours) * 60);
                        
                        if (minutes > 0) {
                            durationSpan.textContent = hours + ' час/а и ' + minutes + ' минути';
                        } else {
                            durationSpan.textContent = hours + ' час/а';
                        }
                    } else {
                        durationSpan.textContent = diffMinutes + ' минути';
                    }
                    
                    totalPriceSpan.textContent = totalPrice.toFixed(2);
                    priceCalculation.style.display = 'block';
                } else {
                    priceCalculation.style.display = 'none';
                }
            }
        }

        startTimeInput.addEventListener('change', calculatePrice);
        endTimeInput.addEventListener('change', calculatePrice);
        
        // Calculate on page load if values exist
        if (startTimeInput.value && endTimeInput.value) {
            calculatePrice();
        }
    </script>
</body>
</html>