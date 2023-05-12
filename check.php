<?php

$apikeys = array(
    // array of api keys
    );

$current = $apikeys[array_rand($apikeys)];

$url = 'https://developers.checkphish.ai/api/neo/scan';


if(!isset($_GET['url'])) {
    die("Please enter an URL");
}

$sus = $_GET['url'];

$data = array(
	"apiKey" => $current,
	"urlInfo" =>  array("url" => $sus) 
);

$json_data = json_encode($data);


$headers = array('Content-Type: application/json');


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if(curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
}

curl_close($ch);

$prefinal = json_decode($response, true);

$jobID = $prefinal["jobID"];

//-----------------------------------------------------------------------------

$url = 'https://developers.checkphish.ai/api/neo/scan/status';

$data = array(
	"apiKey"   => $current,
	"jobID"    => $jobID,
	"insights" => false
);

$json_data = json_encode($data);


$headers = array('Content-Type: application/json');


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$status = "PENDING";

while ($status == "PENDING"){
    sleep(1);
    $response = curl_exec($ch);
    $final = json_decode($response, true);
    $status = $final["status"];
}
if(curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
}

curl_close($ch);

if($final["disposition"] == "clean") {
    echo "This looks like a good URL.";
} else {
    echo "Suspicious URL. Don't proceed.";
}

?>
