<?php

header("Content-Type: application/json");
$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$pdo = null;
try {
    $pdo = new PDO(
        sprintf(
            "mysql:host=%s;dbname=%s;charset=utf8mb4",
            getenv("MYSQL_HOST") ?: "mysql",
            getenv("MYSQL_DATABASE") ?: "projecte_db"
        ),
        getenv("MYSQL_USER") ?: "projecte_user",
        getenv("MYSQL_PASSWORD") ?: "change_me_user_pw"
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB error"]);
    exit();
}

$redis = new Redis();
$redis->connect("redis", 6379);

if ($path === "/articles") {
    if ($method === "GET") {
        $stmt = $pdo->query(
            "SELECT id, user_id, title, content, published_at FROM articles ORDER BY published_at DESC"
        );
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    if ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            $data = $_POST;
        }
        if (empty($data["title"]) || empty($data["content"])) {
            http_response_code(400);
            echo json_encode(["error" => "missing fields"]);
            exit();
        }
        $stmt = $pdo->prepare(
            "INSERT INTO articles (user_id, title, content, published_at) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([
            $data["user_id"] ?: 1,
            $data["title"],
            $data["content"],
        ]);
        // invalidate cache (simple)
        $redis->del("latest_articles");
        http_response_code(201);
        echo json_encode(["id" => $pdo->lastInsertId()]);
        exit();
    }
}

if ($path === "/stats") {
    $visits = $redis->get("visits_count") ?: 0;
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $articleCount = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    echo json_encode([
        "visits" => (int) $visits,
        "users" => (int) $userCount,
        "articles" => (int) $articleCount,
    ]);
    exit();
}

http_response_code(404);
echo json_encode(["error" => "not found"]);
