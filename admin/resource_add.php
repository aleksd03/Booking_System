<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get all categories
$conn = getDbConnection();
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
closeDbConnection($conn);

// Get old form data if validation failed
$old_data = $_SESSION['resource_data'] ?? [];
unset($_SESSION['resource_data']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавяне на ресурс - ADBook</title>
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
        <div class="form-container form-container-medium">
            <h2 class="page-title">Добавяне на нов ресурс</h2>
            
            <?php
            // Display errors if any
            if (isset($_SESSION['resource_errors'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['resource_errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['resource_errors']);
            }
            ?>

            <form action="resource_add_process.php" method="POST">
                <div class="form-group">
                    <label for="name">Име на ресурса: *</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($old_data['name'] ?? ''); ?>"
                           placeholder="Например: Конферентна зала А">
                </div>

                <div class="form-group">
                    <label for="category_id">Категория: *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Изберете категория --</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>"
                                    <?php echo (isset($old_data['category_id']) && $old_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Описание: *</label>
                    <textarea id="description" name="description" rows="3" required
                              placeholder="Кратко описание на ресурса..."><?php echo htmlspecialchars($old_data['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="capacity">Капацитет (човека):</label>
                        <input type="number" id="capacity" name="capacity" min="1" max="1000"
                               value="<?php echo htmlspecialchars($old_data['capacity'] ?? ''); ?>"
                               placeholder="Например: 20">
                        <small class="helper-text">Оставете празно, ако не е приложимо</small>
                    </div>

                    <div class="form-group">
                        <label for="price_per_hour">Цена на час (лв): *</label>
                        <input type="number" id="price_per_hour" name="price_per_hour" 
                               min="0" step="0.01" required
                               value="<?php echo htmlspecialchars($old_data['price_per_hour'] ?? ''); ?>"
                               placeholder="Например: 50.00">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Локация: *</label>
                    <input type="text" id="location" name="location" required
                           value="<?php echo htmlspecialchars($old_data['location'] ?? ''); ?>"
                           placeholder="Например: Етаж 2, Стая 201">
                </div>

                <div class="form-group">
                    <label for="status">Статус: *</label>
                    <select id="status" name="status" required>
                        <option value="available" <?php echo (isset($old_data['status']) && $old_data['status'] === 'available') ? 'selected' : 'selected'; ?>>
                            Наличен
                        </option>
                        <option value="unavailable" <?php echo (isset($old_data['status']) && $old_data['status'] === 'unavailable') ? 'selected' : ''; ?>>
                            Недостъпен
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✅ Добави ресурс
                    </button>
                    <a href="resources.php" class="btn btn-cancel">
                        ← Отказ
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>