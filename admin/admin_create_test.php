<?php
include '../config.php';
include '../header.php';

if ($_SESSION['role'] != 'admin') {
    echo "Доступ заборонено!";
    exit;
}

// Додавання тесту
if (isset($_POST['add_test'])) {
    $title = $_POST['title'];
    $stmt = $conn->prepare("INSERT INTO tests (title) VALUES (?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $test_id = $stmt->insert_id;
    echo "<p>Тест створено! Додавайте запитання нижче.</p>";
}

// Додавання запитань та відповідей
if (isset($_POST['add_question'])) {
    $test_id = $_POST['test_id'];
    $question_text = $_POST['question_text'];
    $stmt = $conn->prepare("INSERT INTO questions (test_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $test_id, $question_text);
    $stmt->execute();
    $question_id = $stmt->insert_id;

    foreach ($_POST['answers'] as $index => $answer_text) {
        $is_correct = isset($_POST['is_correct'][$index]) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $question_id, $answer_text, $is_correct);
        $stmt->execute();
    }
    echo "<p>Запитання додано до тесту!</p>";
}
?>
<link rel="stylesheet" href="/labsWeb/lab_7/styletest.css">
<h2>Створити новий тест</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Назва тесту" required>
    <button type="submit" name="add_test">Створити тест</button>
</form>

<?php if (isset($test_id)): ?>
    <h3>Додати запитання до тесту</h3>
    <form method="POST">
        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
        <input type="text" name="question_text" placeholder="Текст запитання" required>
        <div id="answers">
    <div>
        <input type="text" name="answers[]" placeholder="Варіант відповіді" required>
        <input type="checkbox" name="is_correct[0]"> Правильна відповідь
    </div>
</div>
        <button type="button" onclick="addAnswer()">Додати ще варіант відповіді</button>
        <button type="submit" name="add_question">Додати запитання</button>
    </form>

    <script>
        let answerCount = 1;
        function addAnswer() {
            const answerDiv = document.createElement('div');
            answerDiv.innerHTML = `<input type="text" name="answers[]" placeholder="Варіант відповіді" required>
                                   <input type="checkbox" name="is_correct[${answerCount}]"> Правильна відповідь`;
            document.getElementById('answers').appendChild(answerDiv);
            answerCount++;
        }
    </script>
<?php endif; ?>

<?php include '../footer.php'; ?>
