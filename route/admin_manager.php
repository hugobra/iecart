<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
include_once '../classes/request.class.php';
include_once '../classes/manager.class.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

if ($_SESSION['rang'] < Rangs::ADMINSITE){
	header("Location: /route/stages.php?permission_denied");
}

$siteActuel = new Site($_SESSION['site']);

$managers = Manager::getAllFromSite($siteActuel);
$rangs = Rang::getAll();
$services = Service::getAllFromSite($siteActuel);
$equipes = Equipe::getAllFromSite($siteActuel);
$tranches = Tranche::getAllFromSite($siteActuel);

if (isset($_GET['idSuppr'])){
	$managerADelete = new Manager((string) $_GET['idSuppr']);

	$managerADelete->delete();

	header("Location: /route/admin_manager.php");
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_manager.tpl.html");
else
	include("connexion.php");

?>
