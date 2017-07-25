<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();

$date = date("Y-m-d");

if ($_SESSION['rang'] < Rangs::ADMINSITE){
	header("Location: /route/stages.php?permission_denied");
}


if ($_SESSION['rang'] < Rangs::SUPERADMIN && $_SESSION['rang'] >= Rangs::ADMINSITE){
	$agents = UserManager::getAllAgentsFromSite($_SESSION['site']);
}else if($_SESSION['rang'] >= Rangs::SUPERADMIN ){
	$agents = UserManager::getAllAgents($_SESSION['site']);
}

$class = new ReflectionClass("Rangs");
$constantsRangs = $class->getConstants();

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
	echo"<script>window.location='admin.php';</script>";
}

if(isset($_GET['suppr'])){
	if ($_SESSION['rang'] < Rangs::SUPERADMIN && $_SESSION['rang'] >= Rangs::ADMINSITE){
		UserManager::deleteAgentFromNNIAndSite($_GET['suppr'], $_SESSION['site']);
	}else if($_SESSION['rang'] >= Rangs::SUPERADMIN ){
		UserManager::deleteAgentFromNNIAndSite($_GET['suppr'], $_GET['site']);
	}
	echo"<script>window.location='admin.php';</script>";
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin.tpl.html");
else
	include("connexion.php");

?>
