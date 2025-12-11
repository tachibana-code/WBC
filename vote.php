<?php
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// ------------------------------------
// ✅ 修正後の設定
// ------------------------------------
// サービス名 'db' をホスト名として指定
$servername = "db"; 
$username = "user";
$password = "password123";
$dbname = "wbc_db"; 
// ------------------------------------

// POSTデータからteamIdを取得 (HTMLのJSで `body: teamId=${teamId}` の形式で送信されている)
$teamId = isset($_POST['teamId']) ? $_POST['teamId'] : '';

// teamIdのバリデーション
$validTeams = ['japan', 'usa', 'dr', 'mexico'];
if (!in_array($teamId, $validTeams)) {
    $response['message'] = '無効なチームIDです。';
    echo json_encode($response);
    exit;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // トランザクション開始
    $conn->beginTransaction();

    // 1. 投票数をインクリメント
    // team_idが存在しない場合はINSERT（保険として）、存在する場合はUPDATE
    $sql_update = "
        INSERT INTO vote_data (team_id, vote_count) VALUES (:team_id, 1)
        ON DUPLICATE KEY UPDATE vote_count = vote_count + 1
    ";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':team_id', $teamId);
    $stmt_update->execute();

    // 2. 最新の投票数を取得
    $sql_select = "SELECT vote_count FROM vote_data WHERE team_id = :team_id";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bindParam(':team_id', $teamId);
    $stmt_select->execute();
    $result = $stmt_select->fetch(PDO::FETCH_ASSOC);
    
    $current_count = $result ? (int)$result['vote_count'] : 0;

    $conn->commit(); // コミット

    $response['success'] = true;
    $response['current_count'] = $current_count;
    $response['message'] = '投票が成功しました。';

} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack(); // エラー時はロールバック
    }
    http_response_code(500);
    $response['message'] = '投票処理中にデータベースエラーが発生しました。';
    // $response['debug'] = $e->getMessage(); // デバッグ用
}

$conn = null;
echo json_encode($response);
?>