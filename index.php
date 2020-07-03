<?php

	require 'upload.php';

	$fileToUpload = $_FILES["file"];

	$upload = new UploadFile();
	$upload->startUpload($fileToUpload);

?>