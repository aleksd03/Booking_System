<?php
session_start();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - ADBook</title>
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
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Регистрация</h2>
            
            <?php
            // Display errors if any
            if (isset($_SESSION['registration_errors'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['registration_errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['registration_errors']);
            }
            
            // Get old form data if validation failed
            $old_data = $_SESSION['registration_data'] ?? [];
            unset($_SESSION['registration_data']);
            ?>
            
            <form action="register_process.php" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="name">Име и фамилия:</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($old_data['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Имейл:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" id="phone" name="phone" required
                           value="<?php echo htmlspecialchars($old_data['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Парола:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Потвърдете паролата:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Регистрация</button>
            </form>

            <p style="text-align: center; margin-top: 1rem;">
                Вече имате акаунт? <a href="login.php" style="color: #3498db;">Влезте тук</a>
            </p>
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
