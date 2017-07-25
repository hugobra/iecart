<?php
include_once '../classes/tranche.class.php';
include_once '../classes/bdd.class.php';

$bdd = new BDD();

if (isset($_GET['site'])){
$tranches = array();

		$trancheQuery = $bdd->_connexion->Query("SELECT * FROM tranches WHERE idSite ='".$_GET['site']."'");
		if (!$trancheQuery){
			return false;
		}
		$trancheQuery->setFetchMode(PDO::FETCH_OBJ);
		while($trancheFetch = $trancheQuery->fetch()){
			array_push($tranches, new Tranche($trancheFetch->idTranche, $trancheFetch->nomTranche));
		}
		
}
exit(json_encode($tranches));
?>