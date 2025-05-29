<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    foreach ($result['Items'] as $item) {
        $winner = $item['winner']['S'];
        $loser = $item['loser']['S'];
        $date = date('Y-m-d H:i:s', $item['timestamp']['N']);
        echo "<p><strong>Time:</strong> $date &nbsp; | &nbsp; <strong>Kazanan:</strong> $winner &nbsp; | &nbsp; <strong>Kaybeden:</strong> $loser</p>";
    }
} catch (AwsException $e) {
    echo "Hata: " . $e->getMessage();
}
?>
