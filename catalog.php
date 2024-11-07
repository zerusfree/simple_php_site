<?php
include 'config.php';
include 'header.php';

$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

// Оновлений запит, що включає кількість доступних книг
$query = "SELECT books.book_id, books.title, authors.name AS author, genres.genre_name AS genre, 
          books.publication_year, books.quantity
          FROM books 
          JOIN authors ON books.author_id = authors.author_id
          JOIN genres ON books.genre_id = genres.genre_id";

$params = [];
if ($genre) {
    $query .= " WHERE genres.genre_name = ?";
    $params[] = $genre;
}

// Дозволені поля для сортування
$allowed_sort_columns = ['title', 'author', 'publication_year', 'quantity'];
if (in_array($sort, $allowed_sort_columns)) {
    $query .= " ORDER BY $sort DESC";
} else {
    $query .= " ORDER BY title";
}

// Виконання запиту з фільтрацією за жанром (якщо вибрано)
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
        <option value="quantity" <?php if ($sort === 'quantity') echo 'selected'; ?>>Наявність</option>
    </select>
    <button type="submit">Застосувати</button>
</form>

<table border="1">
    <tr>
        <th>Назва</th>
        <th>Автор</th>
        <th>Жанр</th>
        <th>Рік видання</th>
        <th>Наявність</th>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <th>Дії</th>
        <?php endif; ?>
        <th>Позичити</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['genre']); ?></td>
            <td><?php echo htmlspecialchars($row['publication_year']); ?></td>
            <td><?php echo $row['quantity'] > 0 ? $row['quantity'] . ' доступно' : 'Недоступно'; ?></td>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <td><a href="/labsWeb/lab_7/admin/edit_book.php?id=<?php echo $row['book_id']; ?>">Редагувати</a></td>
            <?php endif; ?>
            <td>
                <?php if ($row['quantity'] > 0): ?>
                    <a href="/labsWeb/lab_7/loans.php?book_id=<?php echo $row['book_id']; ?>">Позичити</a>
                <?php else: ?>
                    Недоступно
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
