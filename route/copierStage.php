<?php
include_once '../config.php';
include_once '../classes/fap.class.php';
include_once '../classes/theme.class.php';
include_once '../classes/fonction.class.php';
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

// recupération du stage que l'on souhaite copier par l'id

$stage = new Stage($_GET['id']);

$site = new Site($_SESSION['site']);

if (isset($_POST['titre'], $_POST['duree'])){
	
	Stage::creer($site, $date, $_POST['titre'], $_POST['duree'], $_POST['codeAction']);

	$stageCree = Stage::getLastStageForSite($site);

	// recuperation des objectifs a copier, puis execution
	$objectifs=$stage->getObjectifs();
	
	foreach ($objectifs as $objectif)
	{
		
		$fonction=$objectif->_fonction->_id;
		$nomobj=$objectif->_nom;
		//echo $fonction;
		//echo $nomobj;
			
		
		$stageCree->addObjectif($nomobj,$fonction);
		$objstages=$stageCree->getObjectifs();
	$opss=Stage::getAllFromStageObjectifAndFonction($stage,$objectif->_id, $fonction);
	foreach($objstages as $objstage){ if($objstage->_nom==$nomobj && $objstage->_fonction->_id==$fonction){
		foreach ($opss as $ops){
		
		$rajouterOPS=Stage::addOPS($stageCree->_id,$objstage->_id, $fonction,$ops->_nom);
		}}}
	}
	;
	
	
	
	// Redirection vers la liste des stages
	
	echo"<script>window.location='ficheStage.php?id=".$stageCree->_id."';</script>";
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/copier_stage.tpl.html");
else
	include("connexion.php");

?>
