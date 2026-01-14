<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get resource ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Невалиден ресурс";
    header('Location: resources.php');
    exit();
}

$resource_id = intval($_GET['id']);

// Get resource details
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Ресурсът не съществува";
    $stmt->close();
    closeDbConnection($conn);
    header('Location: resources.php');
    exit();
}

$resource = $result->fetch_assoc();
$stmt->close();

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
closeDbConnection($conn);

// Get old form data if validation failed (otherwise use current resource data)
$old_data = $_SESSION['resource_data'] ?? $resource;
unset($_SESSION['resource_data']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактиране на ресурс - ADBook</title>
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
            <h2 class="page-title">
                Редактиране: <?php echo htmlspecialchars($resource['name']); ?>
            </h2>
            
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

            <form action="resource_edit_process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $resource_id; ?>">
                
                <div class="form-group">
                    <label for="name">Име на ресурса: *</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($old_data['name']); ?>">
                </div>

                <div class="form-group">
                    <label for="category_id">Категория: *</label>
                    <select id="category_id" name="category_id" required>
                        <?php 
                        $categories->data_seek(0); // Reset pointer
                        while ($category = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $category['id']; ?>"
                                    <?php echo ($old_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Описание: *</label>
                    <textarea id="description" name="description" rows="3" required><?php echo htmlspecialchars($old_data['description']); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="capacity">Капацитет (човека):</label>
                        <input type="number" id="capacity" name="capacity" min="1" max="1000"
                               value="<?php echo htmlspecialchars($old_data['capacity'] ?? ''); ?>">
                        <small class="helper-text">Оставете празно, ако не е приложимо</small>
                    </div>

                    <div class="form-group">
                        <label for="price_per_hour">Цена на час (лв): *</label>
                        <input type="number" id="price_per_hour" name="price_per_hour" 
                               min="0" step="0.01" required
                               value="<?php echo htmlspecialchars($old_data['price_per_hour']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Локация: *</label>
                    <input type="text" id="location" name="location" required
                           value="<?php echo htmlspecialchars($old_data['location']); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Статус: *</label>
                    <select id="status" name="status" required>
                        <option value="available" <?php echo ($old_data['status'] === 'available') ? 'selected' : ''; ?>>
                            Наличен
                        </option>
                        <option value="unavailable" <?php echo ($old_data['status'] === 'unavailable') ? 'selected' : ''; ?>>
                            Недостъпен
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✅ Запази промените
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