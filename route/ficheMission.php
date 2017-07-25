<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';


/*
Démarrage de session
*/
session_cache_limiter('private, must-revalidate');
session_start();

$site = new Site($_SESSION['site']);

$mission = new Mission($_GET['id']);



/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/fiche_mission.tpl.html");
else
	include("connexion.php");

?>
