<?php
session_start();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - ADBook</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <h1>ADBook</h1>
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
            <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Вход в системата</h2>
            
            <form action="login_process.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">Имейл:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Парола:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Вход</button>
            </form>

            <p style="text-align: center; margin-top: 1rem;">
                Нямате акаунт? <a href="register.php" style="color: #3498db;">Регистрирайте се тук</a>
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
