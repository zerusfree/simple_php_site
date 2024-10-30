<?php
include 'config.php';
include 'header.php';

// Отримуємо значення параметрів
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

// Формуємо SQL-запит
$query = "SELECT books.book_id, books.title, authors.name AS author, genres.genre_name AS genre, 
          publishers.publisher_name AS publisher, books.publication_year
          FROM books 
          JOIN authors ON books.author_id = authors.author_id
          JOIN genres ON books.genre_id = genres.genre_id
          JOIN publishers ON books.publisher_id = publishers.publisher_id";

// Додаємо фільтрацію за жанром, якщо параметр жанру не порожній
$params = [];
if ($genre) {
    $query .= " WHERE genres.genre_name = ?";
    $params[] = $genre;
}

// Додаємо сортування
$allowed_sort_columns = ['title', 'author', 'publication_year'];
if (in_array($sort, $allowed_sort_columns)) {
    $query .= " ORDER BY $sort";
} else {
    $query .= " ORDER BY title";
}

// Виконуємо запит із підготовленими параметрами
$stmt = $conn->prepare($query);
if ($genre) {
    $stmt->bind_param("s", $genre);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Каталог книг</h2>
<form method="GET">
    <label>Фільтр за жанром:</label>
    <select name="genre">
        <option value="">Всі жанри</option>
        <?php
        // Отримуємо всі жанри для випадаючого списку
        $genres_query = "SELECT genre_name FROM genres";
        $genres_result = $conn->query($genres_query);
        while ($genre_row = $genres_result->fetch_assoc()) {
            $selected = $genre === $genre_row['genre_name'] ? 'selected' : '';
            echo "<option value='" . htmlspecialchars($genre_row['genre_name']) . "' $selected>" . htmlspecialchars($genre_row['genre_name']) . "</option>";
        }
        ?>
    </select>

    <label>Сортування:</label>
    <select name="sort">
        <option value="title" <?php if ($sort === 'title') echo 'selected'; ?>>Назва</option>
        <option value="author" <?php if ($sort === 'author') echo 'selected'; ?>>Автор</option>
        <option value="publication_year" <?php if ($sort === 'publication_year') echo 'selected'; ?>>Рік</option>
    </select>
    <button type="submit">Застосувати</button>
</form>

<table border="1">
    <tr>
        <th>Назва</th>
        <th>Автор</th>
        <th>Жанр</th>
        <th>Видавництво</th>
        <th>Рік видання</th>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <th>Дії</th>
        <?php endif; ?>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['genre']); ?></td>
            <td><?php echo htmlspecialchars($row['publisher']); ?></td>
            <td><?php echo htmlspecialchars($row['publication_year']); ?></td>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <td><a href="/labsWeb/lab_7/admin/edit_book.php?id=<?php echo $row['book_id']; ?>">Редагувати</a></td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
