<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

// Benzersiz ID üretmek için
function uuid() {
    return bin2hex(random_bytes(16));
}

$client = new DynamoDbClient([
    'region'  => 'ap-northeast-1',  // Tokyo bölgesi
    'version' => 'latest',
]);

$item = [
    'id'       => ['S' => uuid()],          // Mevcut partition key
    'winner'   => ['S' => 'ピカチュウ'],     // Yeni alan: kazanan Pokémon
    'loser'    => ['S' => 'フシギダネ'],     // Yeni alan: kaybeden Pokémon
    'date'     => ['S' => date('Y-m-d H:i:s')], // Yeni alan: tarih
];

try {
    $client->putItem([
        'TableName' => 'pokemon_battles',
        'Item' => $item
    ]);
    echo "Savaş sonucu başarıyla kaydedildi!";
} catch (DynamoDbException $e) {
    echo "HATA: " . $e->getMessage();
}
