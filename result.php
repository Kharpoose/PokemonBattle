<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

$client = new DynamoDbClient([
    'region' => 'ap-northeast-3',
    'version' => 'latest',
]);

try {
    $result = $client->scan([
        'TableName' => 'BattleResults',
        'Limit' => 10,
    ]);

    echo "<h2>過去10試合の結果</h2>";

    if (empty($result['Items'])) {
        echo "<p>No Record</p>";
    } else {
        foreach ($result['Items'] as $item) {
            $winner = isset($item['winner']['S']) ? $item['winner']['S'] : 'Bilinmiyor';
            $loser = isset($item['loser']['S']) ? $item['loser']['S'] : 'Bilinmiyor';
            $timestamp = isset($item['timestamp']['N']) ? (int)$item['timestamp']['N'] : 0;
            $date = $timestamp > 0 ? date('Y-m-d H:i:s', $timestamp) : 'No Data';

            echo "<p><strong>バトル開始時間:</strong> $date &nbsp; | &nbsp; <strong>勝者:</strong> $winner &nbsp; | &nbsp; <strong>敗者:</strong> $loser</p>";
        }
    }
} catch (AwsException $e) {
    echo "Error: " . $e->getMessage();
}
