<?php
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('Asia/Tokyo');
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$client = new DynamoDbClient([
    'region' => 'ap-northeast-3',
    'version' => 'latest',
]);

$winner = $_POST['winner'] ?? null;

if (!$winner || !in_array($winner, ['sensin', 'rakip'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Geçerli bir kazanan belirtmelisiniz ("sensin" veya "rakip")']);
    exit;
}

$loser = $winner === 'sensin' ? 'rakip' : 'sensin';

$battleId = uniqid('battle_', true);
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
    echo json_encode(['status' => 'success', 'message' => 'Result DynamoDB\'ye Saved']);
} catch (AwsException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
