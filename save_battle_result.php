<?php
require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$client = new DynamoDbClient([
    'region' => 'ap-northeast-3',
    'version' => 'latest',
    // Eğer IAM rolü yoksa, credentials ekle:
    // 'credentials' => [
    //     'key'    => 'AWS_ACCESS_KEY_ID',
    //     'secret' => 'AWS_SECRET_ACCESS_KEY',
    // ],
]);

$winner = $_POST['winner'] ?? 'rakip';
$loser = $winner === 'sensin' ? 'rakip' : 'sensin';

$battleId = uniqid('battle_');
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
    echo "Hata: " . $e->getAwsErrorMessage();
}
