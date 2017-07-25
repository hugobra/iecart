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

if ($_SESSION['rang'] < Rangs::ADMIN){
	header("Location: /route/stages.php?permission_denied");
}
if(isset($_GET['id'])){
$agent = new UserManager($_GET['id']);
}
/*$class = new ReflectionClass("Rangs");
$constantsRangs = $class->getConstants();*/

$sites = Site::getAll();
$rangs = Rang::getAll();
$siteActuel = new Site($_SESSION['site']);



if (isset($_POST['nniRole']) && isset($_POST['site']) && isset($_POST['rang']) && isset($_POST['mail'])){
	
UserManager::changeRangFromNNIAndSite($_POST['nniRole'],$_POST['rang'],$_POST['site'],$_POST['mail']);
	// Redirection vers la liste des agents
	header("Location: /route/admin_agents.php");
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_agentmodifrole.tpl.html");
else
	include("connexion.php");

?>
