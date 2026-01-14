<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get all resources with category info
$conn = getDbConnection();
$query = "SELECT r.*, c.name as category_name, c.icon as category_icon 
          FROM resources r 
          JOIN categories c ON r.category_id = c.id 
          ORDER BY c.id, r.name";
$resources = $conn->query($query);
closeDbConnection($conn);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление на ресурси - ADBook</title>
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
        <div class="admin-page-header">
            <h2 class="page-title">Управление на ресурси</h2>
            <a href="resource_add.php" class="btn btn-primary">➕ Добави нов ресурс</a>
        </div>

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

        <div class="table-container">
            <?php if ($resources->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Име</th>
                            <th>Категория</th>
                            <th>Капацитет</th>
                            <th>Локация</th>
                            <th>Цена/час</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($resource = $resources->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $resource['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($resource['category_icon']); ?> <?php echo htmlspecialchars($resource['name']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($resource['description'], 0, 50)) . '...'; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($resource['category_name']); ?></td>
                                <td>
                                    <?php 
                                    if ($resource['capacity']) {
                                        echo $resource['capacity'] . ' ' . ($resource['capacity'] == 1 ? 'човек' : 'човека');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($resource['location']); ?></td>
                                <td><strong><?php echo number_format($resource['price_per_hour'], 2); ?> €</strong></td>
                                <td>
                                    <?php if ($resource['status'] === 'available'): ?>
                                        <span class="badge badge-available">Наличен</span>
                                    <?php else: ?>
                                        <span class="badge badge-unavailable">Недостъпен</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="resource_edit.php?id=<?php echo $resource['id']; ?>" class="btn btn-edit">
                                            ✏️ Редактирай
                                        </a>
                                        <button onclick="deleteResource(<?php echo $resource['id']; ?>, '<?php echo htmlspecialchars($resource['name'], ENT_QUOTES); ?>')" 
                                                class="btn btn-delete">
                                            🗑️ Изтрий
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center-muted">Няма налични ресурси</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>

    <script>
        function deleteResource(id, name) {
            if (confirm('Сигурни ли сте, че искате да изтриете ресурс "' + name + '"?\n\nТова ще изтрие и всички свързани резервации!')) {
                window.location.href = 'resource_delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>