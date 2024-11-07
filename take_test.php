<?php
include 'config.php';
include 'header.php';

$test_id = $_GET['test_id'] ?? null;

// Видалення тесту, якщо адміністратор натискає кнопку "Видалити"
if (isset($_GET['delete_test']) && $_SESSION['role'] === 'admin') {
    $delete_test_id = $_GET['delete_test'];
    $stmt = $conn->prepare("DELETE FROM tests WHERE test_id = ?");
    $stmt->bind_param("i", $delete_test_id);
    $stmt->execute();
    echo "<p>Тест видалено!</p>";
}

// Виведення тесту
if ($test_id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Перевірка, чи на всі питання дано відповідь
        $stmt = $conn->prepare("SELECT question_id FROM questions WHERE test_id = ?");
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $questions = $stmt->get_result();

        $allAnswered = true;
        while ($question = $questions->fetch_assoc()) {
            if (empty($_POST['answers'][$question['question_id']])) {
                $allAnswered = false;
                break;
            }
        }

        if (!$allAnswered) {
            echo "<p style='color:red;'>Будь ласка, дайте відповідь на всі питання.</p>";
        } else {
            // Обробка відповідей користувача
            $correctAnswers = 0;
            foreach ($_POST['answers'] as $question_id => $answer_id) {
                $stmt = $conn->prepare("SELECT is_correct FROM answers WHERE answer_id = ?");
                $stmt->bind_param("i", $answer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->fetch_assoc()['is_correct']) {
                    $correctAnswers++;
                }
            }
            echo "<p>Ви відповіли правильно на $correctAnswers запитань!</p>";
            echo '<a href="catalog.php">Повернутись до каталогу</a>';
        }
    } else {
        // Виведення форми для проходження тесту
        $stmt = $conn->prepare("SELECT * FROM questions WHERE test_id = ?");
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $questions = $stmt->get_result();
        ?>

        <h2>Пройдіть тест</h2>
        <form method="POST">
            <?php while ($question = $questions->fetch_assoc()): ?>
                <div class="question-block">
                    <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM answers WHERE question_id = ?");
                    $stmt->bind_param("i", $question['question_id']);
                    $stmt->execute();
                    $answers = $stmt->get_result();
                    while ($answer = $answers->fetch_assoc()): ?>
                        <label class="answer-option">
                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $answer['answer_id']; ?>">
                            <span><?php echo htmlspecialchars($answer['answer_text']); ?></span>
                        </label><br>
                    <?php endwhile; ?>
                </div>
            <?php endwhile; ?>
            <button type="submit" class="submit-button">Завершити тест</button>
        </form>
                        
        <?php
    }
} else {
    // Виведення списку доступних тестів з кнопками видалення для адміністратора
    $tests = $conn->query("SELECT * FROM tests");
    echo "<h2>Доступні тести</h2>";
    while ($test = $tests->fetch_assoc()) {
        echo "<div class='test-item'>";
        echo "<a href='take_test.php?test_id=" . $test['test_id'] . "'>" . htmlspecialchars($test['title']) . "</a>";
        if ($_SESSION['role'] === 'admin') {
            echo " <a href='take_test.php?delete_test=" . $test['test_id'] . "' class='delete-button'>Видалити</a>";
        }
        echo "</div>";
    }
}
include 'footer.php';
?>


<style>
/* Загальний стиль сторінки */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
}

/* Стиль заголовків */
h2 {
    text-align: center;
    color: #333;
    margin-top: 20px;
}

/* Контейнер для тестів */
.test-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    max-width: 600px;
    margin: 10px auto;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Посилання на тест */
.test-item a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
    transition: color 0.3s;
}

.test-item a:hover {
    color: #555;
}

/* Кнопка видалення для адміністраторів */
.delete-button {
    color: #ff4d4d;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    padding: 5px;
    border: 1px solid #ff4d4d;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.delete-button:hover {
    background-color: #ff4d4d;
    color: #fff;
}

/* Стиль форми */
form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Блок питання */
.question-block {
    margin-bottom: 20px;
}
/* Варіанти відповідей */
/* Варіанти відповідей */
.answer-option {
    display: flex;
    align-items: center; /* Вирівнюємо елементи по вертикалі */
    padding: 10px 15px;
    margin: 8px 0;
    background-color: #f5f5f5;
    border-radius: 5px;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
    text-align: left; /* Вирівнюємо весь контент по лівому краю */
}

.answer-option:hover {
    background-color: #eaeaea;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.answer-option input[type="radio"] {
    width: 15px;
    height: 15px;
    margin-right: 5px;
    accent-color: #333;
}

.answer-option span {
    flex-grow: 1;
    text-align: left; /* Вирівнюємо текст по лівому краю */
}

/* Кнопка завершення тесту */
.submit-button {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
}

.submit-button:hover {
    background-color: #555;
}
</style>