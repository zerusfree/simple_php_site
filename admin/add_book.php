<?php
include '../config.php';
include '../header.php';

if ($_SESSION['role'] != 'admin') {
    echo "Доступ заборонено!";
    exit;
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Отримання даних з форми
    $title = trim($_POST['title']);
    $author_id = trim($_POST['author_id']);
    $genre_id = trim($_POST['genre_id']);
    $year = trim($_POST['year']);
    $quantity = trim($_POST['quantity']);
    
    // Перевірка, чи всі поля заповнені
    if (empty($title) || empty($author_id) || empty($genre_id) || empty($year) || empty($quantity)) {
        $error_message = "Будь ласка, заповніть всі поля!";
    } else {
        // Додавання книги в базу даних
        $stmt = $conn->prepare("INSERT INTO books (title, author_id, genre_id, publication_year, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiii", $title, $author_id, $genre_id, $year, $quantity);

        if ($stmt->execute()) {
            echo "<p>Книгу додано!</p>";
        } else {
            echo "<p>Помилка: " . $stmt->error . "</p>";
        }
    }
}
?>

<link rel="stylesheet" href="/labsWeb/lab_7/style.css">

<h2>Додати нову книгу</h2>

<!-- Виведення повідомлення про помилку -->
<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="title" placeholder="Назва книги" value="<?php echo htmlspecialchars($title ?? ''); ?>">
    <input type="number" name="author_id" placeholder="ID автора" value="<?php echo htmlspecialchars($author_id ?? ''); ?>">
    <input type="number" name="genre_id" placeholder="ID жанру" value="<?php echo htmlspecialchars($genre_id ?? ''); ?>">
    <input type="number" name="year" placeholder="Рік видання" value="<?php echo htmlspecialchars($year ?? ''); ?>">
    <input type="number" name="quantity" placeholder="Кількість примірників" value="<?php echo htmlspecialchars($quantity ?? ''); ?>">
    <button type="submit">Додати книгу</button>
</form>

<?php include '../footer.php'; ?>
