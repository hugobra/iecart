<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/stagiaire.class.php';
include_once '../classes/site.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/fap.class.php';
include_once '../classes/objectif.class.php';
/*
Démarrage de session, récupération de la date du jour
*/
session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

$site = new Site($_SESSION['site']);

if(isset($_GET['id'])){
	$stagiaire = new Stagiaire($_GET['id']);

	$stages = Stage::getAllStagesFromSiteAndFonction($site,$stagiaire->_fonction->_id);

	$idFAP = FAP::getNewFapNumber();
	
	$objectifs = Objectif::getAllFromFonction($stagiaire->_fonction);
	
	$objectifsJSON = str_replace("'", "\'", json_encode($objectifs));

	if(!empty($_POST['stage']) && !empty($_POST['codeAction']) && !empty($_POST['codeSession']) && !empty($_POST['fonction']) && !empty($_POST['dateDebut'])&& !empty($_POST['dateFin'])){
		$raportObjectifs = array();

		for ($i=0; $i < count($_POST['objectifId']); $i++) { 
			$rapportObjectif = array("objectifId" => $_POST['objectifId'][$i], "PF" => $_POST['objectifPF'][$i], "PA" => $_POST['objectifPA'][$i]);
			array_unshift($raportObjectifs, $rapportObjectif);
		}

		FAP::create($idFAP, new stage($_POST['stage']), $stagiaire, $site, $_POST['codeAction'], $_POST['codeSession'], $date, $_SESSION['nom2'], $_POST['fonction'], $_POST['dateDebut'], $_POST['dateFin'],$_POST['nbabsence'],$_POST['absenceD'],$_POST['absenceF'],$_POST['statut'], $raportObjectifs);
		
		// Les valeurs restent pendant 30 minutes
		setcookie("stage", $_POST['stage'], time() + 3600 / 2);
		setcookie("codeAction", $_POST['codeAction'], time() + 3600 / 2);
		setcookie("codeSession", $_POST['codeSession'], time() + 3600 / 2);
		setcookie("dateDebut", $_POST['dateDebut'], time() + 3600 / 2);
		setcookie("dateFin", $_POST['dateFin'], time() + 3600 / 2);

		header("Location: /route/ficheStagiaire.php?id=".$_GET['id']);
	} 
	 
	/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
	if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
		include("../tpl/pages/fiche_fap.tpl.html");
	else
		include("connexion.php");
}


?>
