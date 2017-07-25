<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
include_once '../classes/request.class.php';
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

$requests = Request::getAllPending($siteActuel);

if(isset($_GET['valid'])){
	$request = new Request($_GET['valid']);

	if ($_SESSION['rang'] >= Rangs::ADMINSITE && $request->_stagiaire->isOnthisSite($siteActuel) == true){
		$request->accept();
	}

	header("Location: /route/admin_request.php");
}

if(isset($_GET['idSuppr'])){
	$request = new Request($_GET['idSuppr']);

	if ($_SESSION['rang'] >= Rangs::ADMINSITE && $request->_stagiaire->isOnthisSite($siteActuel) == true){
		$request->decline();
	}

	header("Location: /route/admin_request.php");
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_request.tpl.html");
else
	include("connexion.php");

?>
