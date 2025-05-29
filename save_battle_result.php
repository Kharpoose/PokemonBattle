<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

$client = new DynamoDbClient([
    'region' => 'ap-northeast-3', // Osaka bölgesi
    'version' => 'latest',
]);

$tableName = 'pokemon_battles';

try {
    $result = $client->scan([
        'TableName' => $tableName,
    ]);

    echo "<h2>Geçmiş Battle Sonuçları</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Kazanan</th>
                <th>Kaybeden</th>
                <th>Tarih</th>
            </tr>";

    foreach ($result['Items'] as $item) {
        $id = $item['id']['S'];
        $winner = $item['winner']['S'];
        $loser = $item['loser']['S'];
        $date = $item['date']['S'];

        echo "<tr>
                <td>$id</td>
                <td>$winner</td>
                <td>$loser</td>
                <td>$date</td>
              </tr>";
    }

    echo "</table>";

} catch (AwsException $e) {
    echo "Hata: " . $e->getMessage();
}
?>
