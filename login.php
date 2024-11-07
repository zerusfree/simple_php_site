<?php
include 'config.php';
include 'header.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Перевіряємо, чи заповнені поля
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error_message = "Будь ласка, заповніть усі поля.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Виконуємо запит до бази даних
        $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Перевіряємо, чи знайдений користувач
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();

            // Перевіряємо правильність пароля
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Неправильний пароль.";
            }
        } else {
            $error_message = "Користувача не знайдено.";
        }
    }
}
?>

<form method="POST">
    <!-- Виводимо повідомлення про помилку, якщо є -->
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    
    <input type="text" name="username" placeholder="Логін" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
    <input type="password" name="password" placeholder="Пароль">
    <button type="submit">Увійти</button>
</form>

<?php include 'footer.php'; ?>
