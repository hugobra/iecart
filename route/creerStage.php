<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
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

/* Récupération du nouvel id de stage */
$idStageActuel = Stage::getNewStageNumber();

$site = new Site($_SESSION['site']);

if (isset($_POST['titre'], $_POST['duree'])){
	Stage::creer($site, $date, $_POST['titre'], $_POST['duree'], $_POST['codeAction']);

	$stageCree = Stage::getLastStageForSite($site);

	// Redirection vers la liste des stages
	echo"<script>window.location='ficheStage.php?id=".$stageCree->_id."';</script>";
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/creer_stage.tpl.html");
else
	include("connexion.php");

?>
