<?php
require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// DynamoDB client ayarları
$client = new DynamoDbClient([
    'region' => 'ap-northeast-3',
    'version' => 'latest',
]);

// POST ile gelen kazanan bilgisi
$winner = $_POST['winner'] ?? null;

if (!$winner || !in_array($winner, ['sensin', 'rakip'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Geçerli bir kazanan belirtmelisiniz ("sensin" veya "rakip")']);
    exit;
}

$loser = $winner === 'sensin' ? 'rakip' : 'sensin';

// Benzersiz battle_id
$battleId = uniqid('battle_');

// Zaman damgası
$timestamp = time();

$item = [
    'battle_id' => ['S' => $battleId],
    'timestamp' => ['N' => (string)$timestamp],
    'winner' => ['S' => $winner],
    'loser' => ['S' => $loser],
];

try {
    $client->putItem([
        'TableName' => 'BattleResults',
        'Item' => $item,
    ]);
    echo json_encode(['status' => 'success', 'message' => 'Sonuç DynamoDB\'ye kaydedildi']);
} catch (AwsException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
