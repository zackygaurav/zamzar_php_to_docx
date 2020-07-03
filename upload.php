<?php 

	class UploadFile
	{
		function startUpload($fileToUpload)
		{						
			// JSON Object for Response
			if (!isset($jsonResponse)) 
			{
				$jsonResponse = new stdClass();
			}

			$endpoint = "https://sandbox.zamzar.com/v1/jobs";
			$apiKey = "6d88ba465775c1555f718440a45fba4eb14b9194";
			$targetFormat = "docx";

			$targetDir = "uploads/";
			// Create Directory "uploads/" if not exists (Start)
			if (!is_dir($targetDir)) 
			{
				mkdir($targetDir, 0777, true);
			}
			// Create Directory "uploads/" if not exists (End)
	
			$targetFile = $targetDir . basename($fileToUpload["name"]);	
			
			$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

			if ($fileToUpload["size"] > 1000000) 
			{
				$jsonResponse -> status_code = 400;
				$jsonResponse -> success = false;
				$jsonResponse -> message = "You are on a Free Tier. File size larger than 1 MB is not supported in this Package. Please upgrade to Premium Tier.";

				print(json_encode($jsonResponse));

				return;
			}

			if ($imageFileType != "pdf") 
			{
				$jsonResponse -> status_code = 400;
				$jsonResponse -> success = false;
				$jsonResponse -> message = "Only PDF files are allowed.";
				
				print(json_encode($jsonResponse));

				return;
			}

			if (move_uploaded_file($fileToUpload["tmp_name"], $targetFile)) 
			{
				$jsonResponse -> status_code = 200;
				$jsonResponse -> success = true;
				$jsonResponse -> pdf_url = "http://www.itechvalley.in/gaurav_test/zamzar/v2/" . $targetFile;
				$jsonResponse -> message = "The File " . basename($fileToUpload["name"]) . " has been uploaded";
			}
			else
			{
				$jsonResponse -> status_code = 400;
				$jsonResponse -> success = false;
				$jsonResponse -> message = "Sorry couldn't upload File " . basename($fileToUpload["name"]);

				print(json_encode($jsonResponse));

				return;
			}

			// Job Conversion
			if (!isset($job_data)) 
			{
				$job_data = new stdClass();
			}

			// Since PHP 5.5+ CURLFile is the preferred method for uploading files
			if(function_exists('curl_file_create')) 
			{
			  $sourceFile = curl_file_create($targetFile);
			} 
			else 
			{
			  $sourceFile = '@' . realpath($targetFile);
			}

			$postData = array(
			  "source_file" => $sourceFile,
			  "target_format" => $targetFormat
			);

			$ch = curl_init(); // Init curl
			curl_setopt($ch, CURLOPT_URL, $endpoint); // API endpoint
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // Enable the @ prefix for uploading files
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
			curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":"); // Set the API key as the basic auth username
			$body = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($body, true);

			$job_data -> job_id = $response["id"];
			$job_data -> unique_key = $response["key"];
			$job_data -> message = "File conversion started.";
			$job_data -> create_at = $response["created_at"];
			$job_data -> finished_at = $response["finished_at"];
			$job_data -> status = $response["status"];
			$job_data -> target_format = $response["target_format"];
			$job_data -> credit_cost = $response["credit_cost"];
			
			$jsonResponse -> job_data = $job_data;
			print(json_encode($jsonResponse));	
			// print_r($response);
		}
	}
?>