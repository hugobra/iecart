<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
include_once '../librairie/exportSQL/export.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

if ($_SESSION['rang'] < Rangs::ADMINSITE){
	header("Location: /route/stages.php?permission_denied");
}

if (isset($_POST['nomSite'])){
	Site::create($_POST['nomSite']);
}

if (isset($_GET['idSuppr'])){
	$siteTodelete = new Site($_GET['idSuppr']);

	$siteTodelete->delete();
}

/* Récupération du nouvel id de stage */
$idStageActuel = Stage::getNewStageNumber();

$sites = Site::getAll();

$siteActuel = new Site($_SESSION['site']);

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_site.tpl.html");
else
	include("connexion.php");

?>
