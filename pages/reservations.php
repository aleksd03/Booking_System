<?php
session_start();
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Налични ресурси - ADBook</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="my_reservations.php">Моите резервации</a></li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="../admin/dashboard.php">Админ панел</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Изход (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Вход</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Налични ресурси за резервация</h2>
        
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

        <!-- Category Filter -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="reservations.php" class="btn <?php echo !isset($_GET['category']) ? 'btn-primary' : ''; ?>" 
               style="margin: 0.5rem; <?php echo !isset($_GET['category']) ? '' : 'background-color: #95a5a6; color: white;'; ?>">
                Всички
            </a>
            <?php
            // Get all categories
            $conn = getDbConnection();
            $categories_query = "SELECT * FROM categories ORDER BY name";
            $categories_result = $conn->query($categories_query);
            
            while ($category = $categories_result->fetch_assoc()) {
                $active = (isset($_GET['category']) && $_GET['category'] == $category['id']);
                $btn_class = $active ? 'btn-primary' : '';
                $btn_style = !$active ? 'background-color: #95a5a6; color: white;' : '';
                
                echo '<a href="reservations.php?category=' . $category['id'] . '" class="btn ' . $btn_class . '" 
                      style="margin: 0.5rem; ' . $btn_style . '">';
                echo htmlspecialchars($category['icon']) . ' ' . htmlspecialchars($category['name']);
                echo '</a>';
            }
            ?>
        </div>

        <!-- Resources Grid -->
        <div class="resources-grid">
            <?php
            // Build query based on category filter
            $where_clause = "WHERE r.status = 'available'";
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category_id = intval($_GET['category']);
                $where_clause .= " AND r.category_id = $category_id";
            }
            
            // Get resources
            $query = "SELECT r.*, c.name as category_name, c.icon as category_icon 
                      FROM resources r 
                      JOIN categories c ON r.category_id = c.id 
                      $where_clause 
                      ORDER BY c.id, r.name";
            
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                while ($resource = $result->fetch_assoc()) {
                    ?>
                    <div class="resource-card">
                        <div class="resource-header">
                            <h3><?php echo htmlspecialchars($resource['category_icon']); ?> <?php echo htmlspecialchars($resource['name']); ?></h3>
                            <span class="resource-category"><?php echo htmlspecialchars($resource['category_name']); ?></span>
                        </div>
                        
                        <div class="resource-body">
                            <p class="resource-description"><?php echo htmlspecialchars($resource['description']); ?></p>
                            
                            <div class="resource-details">
                                <?php if ($resource['capacity']): ?>
                                    <div class="detail-item">
                                        <strong>👥 Капацитет:</strong> 
                                        <?php
                                            $capacity = $resource['capacity'];
                                            echo htmlspecialchars($capacity) . ' ' . ($capacity == 1 ? 'човек' : 'човека');
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($resource['location']): ?>
                                    <div class="detail-item">
                                        <strong>📍 Локация:</strong> <?php echo htmlspecialchars($resource['location']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="detail-item resource-price">
                                    <strong>💰 Цена:</strong> <?php echo number_format($resource['price_per_hour'], 2); ?> лв/час
                                </div>
                            </div>
                        </div>
                        
                        <div class="resource-footer">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="create_reservation.php?resource_id=<?php echo $resource['id']; ?>" class="btn btn-primary" style="width: 100%;">
                                    📅 Резервирай сега
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary" style="width: 100%;">
                                    🔒 Влезте за да резервирате
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-error" style="text-align: center;">Няма налични ресурси в тази категория.</div>';
            }
            
            closeDbConnection($conn);
            ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>