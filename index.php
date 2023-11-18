<?php
// Подключение к базе данных (замените значения на свои)
$host = 'localhost';
$dbname = 'post_and_comments';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $jsonData = file_get_contents('https://jsonplaceholder.typicode.com/posts');
    $posts = json_decode($jsonData, true);

    $commentsData = file_get_contents('https://jsonplaceholder.typicode.com/comments');
    $comments = json_decode($commentsData, true);

} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

try {
    // Вставка записей в таблицу "posts"
    foreach ($posts as $postBD) {
        $stmt = $pdo->prepare("INSERT INTO posts (id, title, body) VALUES (?, ?, ?)");
        $stmt->execute([$postBD['id'], $postBD['title'], $postBD['body']]);
    }

    // Вставка комментариев в таблицу "comments"
    foreach ($comments as $comment) {
        $stmt = $pdo->prepare("INSERT INTO comments (id, post_id, name, email, body) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$comment['id'], $comment['postId'], $comment['name'], $comment['email'], $comment['body']]);
    }

    echo "Загружено " . count($posts) . " записей и " . count($comments) . " комментариев\n";

} catch (PDOException $e) {
    error_log("Ошибка: " . $e->getMessage());
    echo "В базе есть " . count($posts) . " записей и " . count($comments) . " комментариев\n" ;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск записей по комментариям</title>
</head>
<body>

    <h1>Поиск записей по комментариям</h1>

    <form action="" method="POST"> <!-- Исправлено на POST -->
        <label for="search">Поиск по тексту комментария:</label>
        <input type="text" name="search" id="search" required>
        <button type="submit" name="submit">Найти</button> <!-- Добавлено name="submit" -->
    </form>

    <?php
    if (isset($_POST['submit'])) { // Исправлено на $_POST
        $searchTerm = $_POST['search'];

        $stmt = $pdo->prepare("SELECT posts.title, comments.body
                              FROM posts
                              JOIN comments ON posts.id = comments.post_id
                              WHERE comments.body LIKE :searchTerm");
        $stmt->execute(['searchTerm' => "%$searchTerm%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h2>Результаты поиска:</h2>";
        if (count($results) > 0) {
            foreach ($results as $result) {
                echo "<p><strong>Заголовок записи:</strong> {$result['title']}</p>";
                echo "<p><strong>Комментарий с искомой строкой:</strong> {$result['body']}</p><hr>";
            }
        } else {
            echo "<p>Ничего не найдено.</p>";
        }
    }
    ?>
</body>
</html>
