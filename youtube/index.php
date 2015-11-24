<?php
	include_once "vendor/autoload.php";
	$client=new Google_Client();
	$client->setApplicationName("Vmax44 youtube1");
	$client->setDeveloperKey("AIzaSyCBnzctBuhhBP-RisGj8-GjkJ8yH5zsU6A");
	$youtube=new Google_Service_YouTube($client);
	$results=$youtube->channels->listChannels('id,snippet',
		['id' => 'UCj2K424HxCZTLWuw-PLBGNA']);
	print_r($result);
?>