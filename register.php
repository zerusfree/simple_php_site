<?php
include 'config.php';
include 'header.php';

$error_message = "";

// Обробка форми, якщо дані були надіслані методом POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Отримання даних з форми
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    
    // Перевірка на заповнення всіх полів
    if (empty($username) || empty($password) || empty($email)) {
        $error_message = "Будь ласка, заповніть всі поля!";
    } else {
        // Хешування пароля
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Вставка нового користувача в базу даних
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            echo "<p>Реєстрація успішна!</p>";
        } else {
            echo "<p>Помилка: " . $stmt->error . "</p>";
        }
    }
}
?>

<!-- HTML-код для форми реєстрації -->
<h2>Реєстрація</h2>

<!-- Виведення повідомлення про помилку, якщо є незаповнені поля -->
<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Логін" value="<?php echo htmlspecialchars($username ?? ''); ?>">
    <input type="password" name="password" placeholder="Пароль">
    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
    <button type="submit">Зареєструватися</button>
</form>

<?php include 'footer.php'; ?>
