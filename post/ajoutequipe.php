<?php
include_once '../classes/service.class.php';
include_once '../classes/manager.class.php';
include_once '../classes/equipe.class.php';
include_once '../classes/tranche.class.php';
include_once '../classes/bdd.class.php';
include_once '../classes/stagiaire.class.php';
include_once '../classes/site.class.php';



if (isset($_POST['service']) && isset($_POST['équipe']) && isset($_POST['tranche']) && isset($_POST['manager']) ){
$bdd = new BDD();
$service=$bdd->_connexion->Quote($_POST['service']);
$équipe=$bdd->_connexion->Quote($_POST['équipe']);
$tranche=$bdd->_connexion->Quote($_POST['tranche']);
$manager=$bdd->_connexion->Quote($_POST['manager']);

$stagiaireQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE service = {$service} AND equipe={$équipe} AND tranche={$tranche}");
$stagiaireQuery->setFetchMode(PDO::FETCH_ASSOC);
	while ($stagiaireFetch = $stagiaireQuery->fetch()){
		
		/*$idQuery = $bdd->_connexion->Query("SELECT idStagiaire FROM stagiaire WHERE nni = {$manager}");
		$idQuery->setFetchMode(PDO::FETCH_OBJ);
		$idfetch = $idQuery->fetch();
		$idmanager=$idfetch->idStagiaire;*/
		if($manager!=$stagiaireFetch['nni']){
	$bdd->_connexion->Query("INSERT INTO managerstaff (nniManager, idStagiaire, idSite) VALUES({$manager}, ".$stagiaireFetch['idStagiaire'].", ".$stagiaireFetch['idSite'].") ON DUPLICATE KEY UPDATE id=id");
		}
	}
}
?>