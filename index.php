<?php
include 'header.php';
include 'config.php';
?>

<h1>Ласкаво просимо до нашої бібліотеки!</h1>

<?php if (isset($_SESSION['username'])): ?>
    <p>Вітаємо, <?php echo htmlspecialchars($_SESSION['username']); ?>! Ви успішно авторизовані.</p>
    <p>Знаходьте цікаві книги, фільтруйте за жанрами, авторами, видавництвами та роками видання!</p>
    <p>Ви можете переглянути <a href="/labsWeb/lab_7/catalog.php">каталог книг</a>.</p>
<?php else: ?>
    <p>Знаходьте цікаві книги, фільтруйте за жанрами, авторами, видавництвами та роками видання!</p>

    <form method="POST" action="">
        <button type="submit" name="create_admin">Створити обліковий запис адміністратора</button>
    </form>

    <?php
    // Обробка кнопки для створення адміністратора
    if (isset($_POST['create_admin'])) {
        $adminUsername = 'admin';
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $adminEmail = 'admin@library.com';
        $adminRole = 'admin';

        // Перевірка, чи існує вже обліковий запис адміністратора
        $checkAdmin = $conn->query("SELECT * FROM users WHERE username = '$adminUsername'");
        if ($checkAdmin->num_rows > 0) {
            echo "<p>Обліковий запис адміністратора вже існує!</p>";
        } else {
            // Додаємо нового адміністратора
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $adminUsername, $adminPassword, $adminEmail, $adminRole);
            
            if ($stmt->execute()) {
                echo "<p>Обліковий запис адміністратора створено! Використовуйте логін <strong>admin</strong> і пароль <strong>admin123</strong> для входу.</p>";
            } else {
                echo "<p>Помилка при створенні адміністратора: " . $stmt->error . "</p>";
            }
        }
    }
endif;

include 'footer.php';
?>
