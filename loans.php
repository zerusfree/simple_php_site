<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Вам потрібно увійти в систему, щоб позичати книги.";
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['book_id'] ?? null;

if (!$book_id) {
    echo "Невірний запит.";
    exit;
}

// Перевірка, чи доступна книга для позичення
$query = "SELECT quantity FROM books WHERE book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    echo "Книга не знайдена.";
    exit;
}

// Якщо книги немає в наявності, виводимо повідомлення і кнопку повернення
if ($book['quantity'] <= 0) {
    echo "Немає книг в наявності.";
    echo '<br><a href="/labsWeb/lab_7/catalog.php"><button>Повернутися до каталогу</button></a>';
    exit;
}

// Додавання запису в таблицю loans
$loan_query = "INSERT INTO loans (user_id, book_id, loan_date) VALUES (?, ?, NOW())";
$loan_stmt = $conn->prepare($loan_query);
$loan_stmt->bind_param("ii", $user_id, $book_id);

if ($loan_stmt->execute()) {
    // Зменшення кількості доступних книг
    $update_query = "UPDATE books SET quantity = quantity - 1 WHERE book_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    echo "Книга успішно позичена!";
} else {
    echo "Помилка при позиченні книги: " . $loan_stmt->error;
}

// Кнопка повернення до каталогу після успішного позичення або помилки
echo '<br><a href="/labsWeb/lab_7/catalog.php"><button>Повернутися до каталогу</button></a>';

$stmt->close();
$loan_stmt->close();
$update_stmt->close();
$conn->close();
?>
