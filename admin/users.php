<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Require admin access
requireAdmin();

// Get all users with their reservation counts
$conn = getDbConnection();
$query = "SELECT u.*, 
          COUNT(r.id) as total_reservations,
          SUM(CASE WHEN r.status = 'confirmed' THEN r.total_price ELSE 0 END) as total_spent
          FROM users u
          LEFT JOIN reservations r ON u.id = r.user_id
          GROUP BY u.id
          ORDER BY u.created_at DESC";

$users = $conn->query($query);
closeDbConnection($conn);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Потребители - ADBook</title>
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
        <h2 class="page-title">Управление на потребители</h2>

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
            <?php if ($users->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Име</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Роля</th>
                            <th>Резервации</th>
                            <th>Общо платено</th>
                            <th>Регистриран</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <?php
                            $registered = new DateTime($user['created_at']);
                            $is_current_user = ($user['id'] == $_SESSION['user_id']);
                            ?>
                            <tr <?php echo $is_current_user ? 'class="row-highlight"' : ''; ?>>
                                <td><strong>#<?php echo $user['id']; ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                    <?php if ($is_current_user): ?>
                                        <br><small class="text-success">(Вие)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge badge-admin">👑 Админ</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">👤 User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['total_reservations'] > 0): ?>
                                        <strong><?php echo $user['total_reservations']; ?></strong> резервации
                                    <?php else: ?>
                                        <span class="text-muted">0 резервации</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['total_spent'] > 0): ?>
                                        <strong class="text-success"><?php echo number_format($user['total_spent'], 2); ?> лв</strong>
                                    <?php else: ?>
                                        <span class="text-muted">0.00 лв</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $registered->format('d.m.Y'); ?>
                                    <br>
                                    <small class="text-muted"><?php echo $registered->format('H:i'); ?></small>
                                </td>
                                <td>
                                    <?php if (!$is_current_user): ?>
                                        <div class="table-actions">
                                            <?php if ($user['role'] === 'user'): ?>
                                                <button onclick="changeRole(<?php echo $user['id']; ?>, 'admin', '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')" 
                                                        class="btn btn-make-admin">
                                                    👑 Направи админ
                                                </button>
                                            <?php else: ?>
                                                <button onclick="changeRole(<?php echo $user['id']; ?>, 'user', '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')" 
                                                        class="btn btn-make-user">
                                                    👤 Направи user
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')" 
                                                    class="btn btn-action">
                                                🗑️ Изтрий
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted text-small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center-muted">Няма регистрирани потребители</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ADBook. Всички права запазени.</p>
        </div>
    </footer>

    <script>
        function changeRole(userId, newRole, userName) {
            const roleText = newRole === 'admin' ? 'администратор' : 'потребител';
            if (confirm('Сигурни ли сте, че искате да направите "' + userName + '" ' + roleText + '?')) {
                window.location.href = 'user_change_role.php?id=' + userId + '&role=' + newRole;
            }
        }

        function deleteUser(userId, userName) {
            if (confirm('ВНИМАНИЕ! Сигурни ли сте, че искате да изтриете потребител "' + userName + '"?\n\nТова ще изтрие и всички негови резервации!')) {
                if (confirm('Последна проверка! Потребителят и всички негови данни ще бъдат изтрити завинаги!')) {
                    window.location.href = 'user_delete.php?id=' + userId;
                }
            }
        }
    </script>
</body>
</html>