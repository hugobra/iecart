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

$agents = UserManager::getAllAgentsFromSite($_SESSION['site']);

$class = new ReflectionClass("Rangs");
$constantsRangs = $class->getConstants();

$sites = Site::getAll();
$rangs = Rang::getAll();
$siteActuel = new Site($_SESSION['site']);

$services = Service::getAllFromSite($siteActuel);
$equipes = Equipe::getAllFromSite($siteActuel);
$tranches = Tranche::getAllFromSite($siteActuel);

if (isset($_POST['nniRole'])){
	if (!isset($_POST['site'])){
		if(isset($_POST['rang']) && $_POST['rang']==4){
			UserManager::ajouterMPL($_POST['nniRole'], $_SESSION['site'],$_POST['service'],$_POST['equipe'],$_POST['tranche']);
		}
		if(isset($_POST['rang']) && $_POST['rang']==5){
			UserManager::ajouterCDS($_POST['nniRole'], $_SESSION['site'],$_POST['service']);
		}
		if(isset($_POST['rang']) && $_POST['rang']>5){
			UserManager::changeRangFromNNIAndSite($_POST['nniRole'], $_POST['rang'], $_SESSION['site']);
		}
		/*UserManager::changeRangFromNNIAndSiteavecfonctionMPL($_POST['nniRole'], $_POST['rang'], $_SESSION['site'],$_POST['service'],$_POST['equipe'],$_POST['tranche']);*/
	}else{
		if(isset($_POST['rang']) && $_POST['rang']==4){
			UserManager::ajouterMPL($_POST['nniRole'], $_POST['site'],$_POST['service'],$_POST['equipe'],$_POST['tranche']);
		}
		if(isset($_POST['rang']) && $_POST['rang']==5){
			UserManager::ajouterCDS($_POST['nniRole'], $_POST['site'],$_POST['service']);
		}
		if(isset($_POST['rang']) && $_POST['rang']>5){
			UserManager::changeRangFromNNIAndSite($_POST['nniRole'], $_POST['rang'], $_POST['site']);
		}
		/*UserManager::changeRangFromNNIAndSiteavecfonctionMPL($_POST['nniRole'], $_POST['rang'], $_POST['site'],$_POST['service'],$_POST['equipe'],$_POST['tranche']);*/
	}

	// Redirection vers la liste des stages
	echo"<script>window.location='admin.php';</script>";
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_role.tpl.html");
else
	include("connexion.php");

?>
