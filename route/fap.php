<?php
include_once '../config.php';
include_once '../classes/fap.class.php';
include_once '../classes/site.class.php';
include_once '../classes/manager.class.php';
include_once '../classes/service.class.php';
include_once '../classes/equipe.class.php';
include_once '../classes/tranche.class.php';
/*
Démarrage de session
*/
session_cache_limiter('private, must-revalidate');
session_start();

$site = new Site($_SESSION['site']);

// Eécupération des FAPS
if ($_SESSION['rang'] > Rangs::CONDUITE){
$faps = FAP::getAllFromSiteWithoutObject($site);
}
if ($_SESSION['rang'] == Rangs::CONDUITE){
$faps = FAP::getAllFromSiteAppuiFormation($site);
}
if ($_SESSION['rang'] == Rangs::MANAGER1){
$manager = new Manager($_SESSION['nni']);

$faps = FAP::getAllFromSiteMPL($site,$manager->_service->_id,$manager->_équipe->_id,$manager->_tranche->_id);
}
if ($_SESSION['rang'] == Rangs::MANAGER2){
$manager = new Manager($_SESSION['nni']);

$faps = FAP::getAllFromSiteCDS($site,$manager->_service->_id);
}

//var_dump($faps); // DEBUG: afficher la liste des faps

/*Transtypage de la variable test*/
if(!empty($_POST['type']))
	$test=(string)$_POST['type'];

/* Suppression des FAP selectionnées*/	
if(!empty($test) && $test=="1"){
	foreach($_POST['idFaps'] as $id){
		$fap = new FAP($id);
		$fap->removefromDatabase();
	}	
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/fap.tpl.html");
else
	include("connexion.php");
	
?>
