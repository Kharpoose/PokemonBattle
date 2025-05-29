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

    echo "<h2>Son 10 Maç Sonucu</h2>";

    if (empty($result['Items'])) {
        echo "<p>Henüz kayıt yok.</p>";
    } else {
        foreach ($result['Items'] as $item) {
            $winner = isset($item['winner']['S']) ? $item['winner']['S'] : 'Bilinmiyor';
            $loser = isset($item['loser']['S']) ? $item['loser']['S'] : 'Bilinmiyor';
            $timestamp = isset($item['timestamp']['N']) ? (int)$item['timestamp']['N'] : 0;
            $date = $timestamp > 0 ? date('Y-m-d H:i:s', $timestamp) : 'Tarih yok';

            echo "<p><strong>Zaman:</strong> $date &nbsp; | &nbsp; <strong>Kazanan:</strong> $winner &nbsp; | &nbsp; <strong>Kaybeden:</strong> $loser</p>";
        }
    }
} catch (AwsException $e) {
    echo "Hata: " . $e->getMessage();
}
