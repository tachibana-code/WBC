<?php
header('Content-Type: application/json');
$response = [];

// ------------------------------------
// ✅ 修正後の設定
// ------------------------------------
// $servername を Docker Compose のサービス名 'db' に変更
$servername = "db"; 
$username = "user";
$password = "password123";
$dbname = "wbc_db"; 
// ------------------------------------

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT team_id, vote_count FROM vote_data");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $votes = [];
    foreach ($results as $row) {
        // team_id をキー、vote_count を値とする連想配列を作成
        $votes[$row['team_id']] = (int)$row['vote_count'];
    }

    echo json_encode($votes);

} catch (PDOException $e) {
    // データベース接続エラーまたはクエリ実行エラー
    http_response_code(500); // サーバーエラーを示す
    $response['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode($response);
}

$conn = null;
?>