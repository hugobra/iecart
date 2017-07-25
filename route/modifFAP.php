<?php
include_once '../config.php';
include_once '../classes/fap.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/rang.class.php';
/*
Démarrage de session, récupération de la date du jour
*/
session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

if ($_SESSION['rang'] < Rangs::FORMATEUR){
	header("Location: /route/stages.php?permission_denied");
}

$site = new Site($_SESSION['site']);
$fap = new FAP($_GET['id']);
$stages = Stage::getAllStagesFromSiteAndFonction($site,$fap->_stagiaire->_fonction->_id);

$objectifs = Objectif::getAllFromFonction($fap->_stagiaire->_fonction);

$objectifsJSON = str_replace("'", "\'", json_encode($objectifs));

if(!empty($_POST['dateDebut']) && !empty($_POST['dateFin']) && !empty($_POST['stage']) && !empty($_POST['codeAction']) && !empty($_POST['codeSession'])){
	$raportObjectifs = array();

	for ($i=0; $i < count($_POST['objectifId']); $i++) { 
		$rapportObjectif = array("objectifId" => $_POST['objectifId'][$i], "PF" => $_POST['objectifPF'][$i], "PA" => $_POST['objectifPA'][$i]);
		array_unshift($raportObjectifs, $rapportObjectif);
	}

	$fap->modifier($_POST['codeAction'], $_POST['codeSession'], $_POST['stage'], $_SESSION['nom2'], $_POST['dateDebut'], $_POST['dateFin'], $_POST['nbabsence'], $_POST['absenceD'], $_POST['absenceF'],$_POST['statut'], $raportObjectifs);
	header("Location: /route/ficheStagiaire.php?id=" . $fap->_stagiaire->_id);
} 

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/modifier_fap.tpl.html");
	else
	include("connexion.php");

?>
