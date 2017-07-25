<?php
include_once '../classes/user.class.php';

$bdd = new BDD();

if (empty($_GET['nni'])&& empty($_GET['site'])){
	exit();
}

		$nniQuote = $bdd->_connexion->Quote($_GET['nni']);
		$siteQuote = $bdd->_connexion->Quote($_GET['site']);

		$userQuery = $bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote} AND Site={$siteQuote}");
		$userQuery->setFetchMode(PDO::FETCH_OBJ);
		$userFetch = $userQuery->fetch();

		if(!isset($userFetch->idUser)){
			return false;
		}

		$user= new UserManager($userFetch->idUser);
	


exit(json_encode($user));
?>