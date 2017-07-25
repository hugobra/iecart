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

$siteActuel = new Site($_SESSION['site']);

$services = Service::getAllFromSite($siteActuel);
$fonctions = Fonction::getAllFromSite($siteActuel);
$equipes = Equipe::getAllFromSite($siteActuel);
$tranches = Tranche::getAllFromSite($siteActuel);

if(isset($_GET['idSupprService'])){
	$serviceADelete = new Service($_GET['idSupprService']);
	$serviceADelete->delete();

	echo"<script>window.location='admin_config.php';</script>";
}

if(isset($_POST['nomService'])){
	if ($_SESSION['rang'] >= Rangs::ADMINSITE){
		$service = new Service(null, $_POST['nomService'], $siteActuel);
		$service->create();

		echo"<script>window.location='admin_config.php';</script>";
	}
}

if(isset($_GET['idSupprEquipe'])){
	$equipeADelete = new Equipe($_GET['idSupprEquipe']);
	$equipeADelete->delete();

	echo"<script>window.location='admin_config.php';</script>";
}

if(isset($_POST['nomEquipe'])){
	if ($_SESSION['rang'] >= Rangs::ADMINSITE){
		$equipe = new Equipe(null, $_POST['nomEquipe'], $siteActuel);
		$equipe->create();

		echo"<script>window.location='admin_config.php';</script>";
	}
}

if(isset($_GET['idSupprTranche'])){
	$trancheADelete = new Tranche($_GET['idSupprTranche']);
	$trancheADelete->delete();

	echo"<script>window.location='admin_config.php';</script>";
}

if(isset($_POST['nomTranche'])){
	if ($_SESSION['rang'] >= Rangs::ADMINSITE){
		$tranche = new Tranche(null, $_POST['nomTranche'], $siteActuel);
		$tranche->create();

		echo"<script>window.location='admin_config.php';</script>";
	}
}

if(isset($_GET['idSupprFonction'])){
	$trancheADelete = new Fonction($_GET['idSupprFonction']);
	$trancheADelete->delete();

	echo"<script>window.location='admin_config.php';</script>";
}

if(isset($_POST['nomFonction'])){
	if ($_SESSION['rang'] >= Rangs::ADMINSITE){
		$tranche = new Fonction(null, $_POST['nomFonction'], $siteActuel);
		$tranche->create();

		echo"<script>window.location='admin_config.php';</script>";
	}
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_config.tpl.html");
else
	include("connexion.php");

?>
