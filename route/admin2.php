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

if ($_SESSION['rang'] < 7){
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
$rangs = Rang::getAll();

$siteActuel = new Site($_SESSION['site']);

if (isset($_POST['nniRole'])){
	if (!isset($_POST['site'])){
		UserManager::changeRangFromNNIAndSite($_POST['nniRole'], $_POST['rang'], $_SESSION['site']);
	}else{
		UserManager::changeRangFromNNIAndSite($_POST['nniRole'], $_POST['rang'], $_POST['site']);
	}

	// Redirection vers la liste des stages
	//echo"<script>window.location='stages.php';</script>";
}

if(isset($_GET['export'])){
	if ($_SESSION['rang'] >= Rangs::SUPERADMIN){
		EXPORT_TABLES(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	}
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin2.tpl.html");
else
	include("connexion.php");

?>
