<?php

	$fileId = $_POST["file_id"];

	$localFilename = "portrait.docx";;
	$endpoint = "https://sandbox.zamzar.com/v1/files/" . $fileId . "/content";
	$apiKey = "6d88ba465775c1555f718440a45fba4eb14b9194";

	$ch = curl_init(); // Init curl
	curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
	curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	$fh = fopen($localFilename, "wb");
	curl_setopt($ch, CURLOPT_FILE, $fh);

	$body = curl_exec($ch);
	curl_close($ch);

	echo "File Downloaded.";