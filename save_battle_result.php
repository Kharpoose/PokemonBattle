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

// Battle sonucu bilgisi - örnek olarak posttan veya mevcut koddan alabilirsiniz
$winner = $_POST['winner'] ?? 'rakip';  // 'sensin' veya 'rakip'
$loser = $winner === 'sensin' ? 'rakip' : 'sensin';

// Benzersiz battle_id oluştur (örn: timestamp + random)
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
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
