<?php
$redis = new Redis();
$redis->connect("redis", 6379);
$visits = $redis->incr("visits_count");

$dsn = sprintf(
    "mysql:host=%s;dbname=%s;charset=utf8mb4",
    getenv("MYSQL_HOST") ?: "mysql",
    getenv("MYSQL_DATABASE") ?: "projecte_db"
);
try {
    $pdo = new PDO(
        $dsn,
        getenv("MYSQL_USER") ?: "projecte_user",
        getenv("MYSQL_PASSWORD") ?: "change_me_user_pw"
    );
    $stmt = $pdo->query(
        "SELECT id, title, published_at FROM articles ORDER BY published_at DESC LIMIT 5"
    );
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $articles = [];
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Frontend - Projecte Final LSR</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Projecte Final - Frontend</h1>
        <p>Visites totals: <?php echo htmlentities($visits); ?></p>
        <h2>Últims articles</h2>
        <ul>
            <?php foreach ($articles as $a): ?>
            <li><?php echo htmlentities($a["title"]); ?> — <?php echo htmlentities(
                $a["published_at"]
            ); ?></li>
            <?php endforeach; ?>
        </ul>


        <h2>Crear article</h2>
        <form method="post" action="https://api.local/articles">
            <p><input name="title" placeholder="Títol"></p>
            <p><textarea name="content" placeholder="Contingut"></textarea></p>
            <p><input type="text" name="user_id" value="1"></p>
            <p><button>Crear</button></p>
        </form>

    </body>
</html>