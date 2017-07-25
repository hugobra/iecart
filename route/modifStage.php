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

$site = new Site($_SESSION['site']);
$stage = new Stage($_GET['id']);
$fonctions = Fonction::getAll();
$themes = $stage->getThemes();

$objectifs = $stage->getObjectifs();

if (isset($_POST['titre'], $_POST['duree'])){
	$stage->modifier($_POST['titre'], $_POST['duree'], $_POST['codeAction']);
	header("Location: ficheStage.php?id=" . $_GET['id']);
}

if(isset($_GET['id']) && isset($_GET['idSuppr'])){
	if (!isset($_GET['type'])){
		$stage->supprimerTheme($_GET['idSuppr']);
		header("Location: modifStage.php?id=" . $_GET['id']);
	}else{
		$stage->supprimerObjectif($_GET['idSuppr']);
		header("Location: ficheStage.php?id=".$_GET['id'] ."#obj".($_GET['idSuppr']-1));
	}
}

if(isset($_POST['objectif']) && $_POST['objectif']!=""){ 
	//En cas d'ajout d'un objectif, execution
	foreach($_POST['fonctionselect'] as $fonction){
	$stage->addObjectif($_POST['objectif'], $fonction);
	}
	header("Location: ficheStage.php?id=" . $_GET['id']);
}

if(isset($_POST['theme']) && $_POST['theme']!=""){ 
	//En cas d'ajout d'un thème, execution
	$stage->addTheme($_POST['theme']);
	header("Location: modifStage.php?id=" . $_GET['id']);
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/modifier_stage.tpl.html");
else
	include("connexion.php");
?>
