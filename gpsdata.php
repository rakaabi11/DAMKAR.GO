<?php 

include_once 'coders.php';

$titik_lokasi = $_GET['latitude'];
$titik_lokasi = $_GET['longitude'];

echo $titik_lokasi;
echo "<br>";


$sql = "INSERT INTO tb_damkar (latitude,longitude) 
	VALUES('".$titik_lokasi."','".$titik_lokasi."')";

if($db_damkar->query($sql) === FALSE)
	{ echo "Error: " . $sql . "<br>" . $db_damkar->error; }

echo "<br>";
echo $db_damkar->insert_id;