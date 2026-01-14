<?php
session_start();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Забравена парола - ADBook</title>
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
            <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Забравена парола</h2>
            
            <?php
            // Display success message if any
            if (isset($_SESSION['forgot_password_message'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['forgot_password_message']) . '</div>';
                unset($_SESSION['forgot_password_message']);
            }
            
            // Display error if any
            if (isset($_SESSION['forgot_password_error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['forgot_password_error']) . '</div>';
                unset($_SESSION['forgot_password_error']);
            }
            ?>
            
            <p style="text-align: center; margin-bottom: 2rem; color: #555;">
                Въведете вашия имейл адрес и ще ви изпратим инструкции за възстановяване на паролата.
            </p>
            
            <form action="forgot_password_process.php" method="POST" id="forgotPasswordForm">
                <div class="form-group">
                    <label for="email">Имейл адрес:</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="example@email.com">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Изпрати инструкции</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="login.php" style="color: #3498db;">← Обратно към вход</a>
            </p>
            
            <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 5px; margin-top: 2rem; border-left: 4px solid #3498db;">
                <p style="margin: 0; color: #555; font-size: 0.9rem;">
                    <strong>Забележка:</strong> Ако не получите имейл, моля свържете се с администратор на 
                    <a href="mailto:admin@adbook.com" style="color: #3498db;">admin@adbook.com</a>
                </p>
            </div>
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