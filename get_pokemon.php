<?php
header('Content-Type: application/json');
 
$host = 'training2025-db-instance-1.c25mkwu8gg8k.us-east-1.rds.amazonaws.com';
$port = '5432';
$dbname = 'salesdb';
$user = 'karpuz';
$password = 'training2025-karpuz';
 
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Bağlantı hatası: ' . $e->getMessage()]);
    exit;
}
 
//name パラメータが渡されていなくてもランダムにポケモンを選ぶ
$name = $_GET['name'] ?? '';

// 名前が指定されていない場合、ランダムでポケモンを取得
if (empty($name)) {
    $stmt = $pdo->query("SELECT * FROM pokemon ORDER BY RANDOM() LIMIT 1");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // 名前が指定されている場合、そのポケモンを取得
    $stmt = $pdo->prepare("SELECT * FROM pokemon WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 結果が空でない場合はJSONとして返す
if (empty($result)) {
    echo json_encode(['error' => 'ポケモンが見つかりませんでした。']);
} else {
    echo json_encode($result); // データをJSON形式で返す
}
    
?>