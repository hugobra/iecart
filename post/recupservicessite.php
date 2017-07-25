<?php
include_once '../classes/service.class.php';
include_once '../classes/bdd.class.php';

$bdd = new BDD();

if (isset($_GET['site'])){
$services = array();

		$serviceQuery = $bdd->_connexion->Query("SELECT * FROM services WHERE idSite ='".$_GET['site']."'");
		if (!$serviceQuery){
			return false;
		}
		$serviceQuery->setFetchMode(PDO::FETCH_OBJ);
		while($serviceFetch = $serviceQuery->fetch()){
			array_push($services, new Service($serviceFetch->idService, $serviceFetch->nomService));
		}
		
}
exit(json_encode($services));
?>