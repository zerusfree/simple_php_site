<?php
include '../config.php';
include '../header.php';

if ($_SESSION['role'] != 'admin') {
    echo "Доступ заборонено!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $genre_id = $_POST['genre_id'];
    $publisher_id = $_POST['publisher_id'];
    $year = $_POST['year'];

    $stmt = $conn->prepare("INSERT INTO books (title, author_id, genre_id, publisher_id, publication_year) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiii", $title, $author_id, $genre_id, $publisher_id, $year);

    if ($stmt->execute()) {
        echo "Книгу додано!";
    } else {
        echo "Помилка: " . $stmt->error;
    }
}
?>
<link rel="stylesheet" href="/labsWeb/lab_7/style.css">
<form method="POST">
    <input type="text" name="title" placeholder="Назва книги" required>
    <input type="number" name="author_id" placeholder="ID автора" required>
    <input type="number" name="genre_id" placeholder="ID жанру" required>
    <input type="number" name="publisher_id" placeholder="ID видавництва" required>
    <input type="number" name="year" placeholder="Рік видання" required>
    <button type="submit">Додати книгу</button>
</form>

<?php include '../footer.php'; ?>
