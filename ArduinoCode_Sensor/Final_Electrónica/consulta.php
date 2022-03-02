<?php
header('Content-Type: application/json');

$pdo=new PDO("mysql:host=127.0.0.1;dbname=id17202099_bdsensor","id17202099_christye","QPsP3SmQaTV_+g)m");
switch($_GET['Consultar']){
		// Buscar �ltimo Dato
		case 1:
		    $statement=$pdo->prepare("SELECT value1 as x FROM SensorData ORDER BY id");
			$statement->execute();
			$results=$statement->fetchAll(PDO::FETCH_ASSOC);
			$json=json_encode($results);
			echo $json;
		break;
		// Buscar Todos los datos
		default:

			$statement=$pdo->prepare("SELECT value1 as x FROM SensorData ORDER BY id DESC LIMIT 0,1");
			$statement->execute();
			$results=$statement->fetchAll(PDO::FETCH_ASSOC);
			$json=json_encode($results);
			echo $json;
		break;

}
?>
