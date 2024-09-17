<?php
// Вызов API и передача данных из файла
$url = 'https://' . $_SERVER['HTTP_HOST'] . '/api/sort.php';
$content = file_get_contents('data/cards-example-01.json');

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt(
    $curl,
    CURLOPT_HTTPHEADER,
    array("Content-type: application/json")
);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($statusCode == 200) {
    $response = json_decode($json_response, true);
    echo $response['description'];
} elseif ($statusCode == 400) {
    $response = json_decode($json_response, true);
    echo "Error: " . $response['error'];
} else {
    echo "Error: API call failed with status $statusCode, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl);
}

curl_close($curl);
