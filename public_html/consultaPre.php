<?php
error_reporting(0);
header('Content-Type: application/json');
$pdo=new PDO("mysql:host=localhost;dbname=id17202099_bdsensor","id17202099_christye","QPsP3SmQaTV_+g)m");


switch(isset($_GET['xd3'])){
		// Buscar �ltimo Dato
		case 1:
		    $statement=$conn->prepare("SELECT value3 as x FROM SensorData ORDER BY id DESC LIMIT 0,1");
			$statement->execute();
			$results=$statement->fetchAll(PDO::FETCH_ASSOC);
			$json=json_encode($results);
			echo $json;
		break;
		// Buscar Todos los datos
		default:

			$statement=$pdo->prepare("SELECT value3 as x FROM SensorData ORDER BY id ASC");
			$statement->execute();
			$results=$statement->fetchAll(PDO::FETCH_ASSOC);
			$json=json_encode($results);
			echo $json;
		break;

}
?>