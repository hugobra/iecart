<?php
include_once '../classes/equipe.class.php';
include_once '../classes/bdd.class.php';

$bdd = new BDD();

if (isset($_GET['site'])){
$equipes = array();

		$equipeQuery = $bdd->_connexion->Query("SELECT * FROM equipes WHERE idSite ='".$_GET['site']."'");
		if (!$equipeQuery){
			return false;
		}
		$equipeQuery->setFetchMode(PDO::FETCH_OBJ);
		while($equipeFetch = $equipeQuery->fetch()){
			array_push($equipes, new Equipe($equipeFetch->idEquipe, $equipeFetch->nomEquipe));
		}
		
}
exit(json_encode($equipes));
?>