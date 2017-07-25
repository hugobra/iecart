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

if(isset($_GET['export'])){
	if ($_SESSION['rang'] >= Rangs::SUPERADMIN){
		EXPORT_TABLES(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	}
}

$siteActuel = new Site($_SESSION['site']);

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_bdd.tpl.html");
else
	include("connexion.php");

?>
