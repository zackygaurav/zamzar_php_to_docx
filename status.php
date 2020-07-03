<?php
$jobID = 15;
$endpoint = "https://sandbox.zamzar.com/v1/jobs/$jobID";
$apiKey = "6d88ba465775c1555f718440a45fba4eb14b9194";

$ch = curl_init(); // Init curl
curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
$body = curl_exec($ch);
curl_close($ch);

$job = json_decode($body, true);

echo "Job:\n----\n";
print_r($job);