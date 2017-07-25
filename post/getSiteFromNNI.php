<?php
include_once '../classes/stagiaire.class.php';

/* permet d'obtenir le site d'un objet de type USER ou STAGIAIRE pour appel AJAX */

$bdd = new BDD();

if (empty($_GET['nni'])){
	exit();
}

$sites = array();

$stagiaire = UserManager::getStagiaireFromNNI($_GET['nni']);
if(!$stagiaire){
		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM users ss JOIN site s ON (ss.idSite = s.idSite) WHERE ss.nni = '".$_GET['nni']."'");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($sites, new Site($stagiaireFetch->idSite));
		}
$infosite=array('sites' => $sites);

}

else{

$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM stagiaire ss JOIN site s ON (ss.idSite = s.idSite) WHERE ss.nni = '".$_GET['nni']."'");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($sites, new Site($stagiaireFetch->idSite));
		}
$infosite=array('sites' => $sites);
}

exit(json_encode($infosite));
?>
