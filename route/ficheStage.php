<?php
include_once '../config.php';
include_once '../classes/site.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/rang.class.php';


/*
Démarrage de session
*/
session_cache_limiter('private, must-revalidate');
session_start();

$site = new Site($_SESSION['site']);

$stage = new Stage($_GET['id']);

$objectifs = $stage->getObjectifs();
$fonctions = Fonction::getAllFromSite($site);
$fonctionstage = Fonction::getAllFromStage($stage);
//$opss = Stage::getOPSfromstage($stage);

$themes = $stage->getThemes();
// var_dump($themes);

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/fiche_stage.tpl.html");
else
	include("connexion.php");

?>
