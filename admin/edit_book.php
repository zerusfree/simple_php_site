<?php
include '../config.php';
include '../header.php';

if ($_SESSION['role'] != 'admin') {
    echo "Доступ заборонено!";
    exit;
}

$book_id = $_GET['id'];
$error_message = "";

// Обробка форми, якщо дані були надіслані методом POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Отримання даних з форми
    $title = trim($_POST['title']);
    $author_id = trim($_POST['author_id']);
    $genre_id = trim($_POST['genre_id']);
    $year = trim($_POST['year']);
    $quantity = trim($_POST['quantity']);
    
    // Перевірка на заповнення всіх полів
    if (empty($title) || empty($author_id) || empty($genre_id) || empty($year) || empty($quantity)) {
        $error_message = "Будь ласка, заповніть всі поля!";
    } else {
        // Оновлення книги в базі даних
        $stmt = $conn->prepare("UPDATE books SET title = ?, author_id = ?, genre_id = ?, publication_year = ?, quantity = ? WHERE book_id = ?");
        $stmt->bind_param("siiiii", $title, $author_id, $genre_id, $year, $quantity, $book_id);

        if ($stmt->execute()) {
            echo "<p>Книгу оновлено!</p>";
        } else {
            echo "<p>Помилка: " . $stmt->error . "</p>";
        }
    }
} else {
    // Отримання інформації про книгу, яку потрібно редагувати
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}
?>

<link rel="stylesheet" href="/labsWeb/lab_7/style.css">

<h2>Редагувати книгу</h2>

<!-- Виведення повідомлення про помилку -->
<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($book['title'] ?? ''); ?>">
    <input type="number" name="author_id" value="<?php echo htmlspecialchars($book['author_id'] ?? ''); ?>">
    <input type="number" name="genre_id" value="<?php echo htmlspecialchars($book['genre_id'] ?? ''); ?>">
    <input type="number" name="year" value="<?php echo htmlspecialchars($book['publication_year'] ?? ''); ?>">
    <input type="number" name="quantity" value="<?php echo htmlspecialchars($book['quantity'] ?? ''); ?>">
    <button type="submit">Оновити книгу</button>
</form>

<?php include '../footer.php'; ?>
