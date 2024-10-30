<?php
include '../config.php';
include '../header.php';

if ($_SESSION['role'] != 'admin') {
    echo "Доступ заборонено!";
    exit;
}

$book_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $genre_id = $_POST['genre_id'];
    $publisher_id = $_POST['publisher_id'];
    $year = $_POST['year'];

    $stmt = $conn->prepare("UPDATE books SET title = ?, author_id = ?, genre_id = ?, publisher_id = ?, publication_year = ? WHERE book_id = ?");
    $stmt->bind_param("siiiii", $title, $author_id, $genre_id, $publisher_id, $year, $book_id);

    if ($stmt->execute()) {
        echo "Книгу оновлено!";
    } else {
        echo "Помилка: " . $stmt->error;
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}
?>
<link rel="stylesheet" href="/labsWeb/lab_7/style.css">
<form method="POST">
    <input type="text" name="title" value="<?php echo $book['title']; ?>" required>
    <input type="number" name="author_id" value="<?php echo $book['author_id']; ?>" required>
    <input type="number" name="genre_id" value="<?php echo $book['genre_id']; ?>" required>
    <input type="number" name="publisher_id" value="<?php echo $book['publisher_id']; ?>" required>
    <input type="number" name="year" value="<?php echo $book['publication_year']; ?>" required>
    <button type="submit">Оновити книгу</button>
</form>

<?php include '../footer.php'; ?>
