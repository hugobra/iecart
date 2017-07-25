<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';
/*
Démarrage de session, récupération de la date du jour
*/
session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");


$site = new Site($_SESSION['site']);
$ecart = new Ecart($_GET['id']);


	$missions = Mission::getAllFromSite($site);

if(!empty($_POST['missionselect']) && !empty($_POST['titre']) && !empty($_POST['description']) && !empty($_POST['propotraitement']) && empty($_POST['traitement'])){
	

	$mission= new Mission($_POST['missionselect']);
		$recepteur=$mission->_responsable;
		echo '<script>alert("'.$ecart->_id.'");</script>' ;
		$ecart->modifier($ecart->_émetteur->_id, $mission->_id, $recepteur,  $_POST['titre'], $_POST['description'], $_POST['propotraitement'], $date, $site );
		
		header("Location: /route/ecarts.php");
} 

if(!empty($_POST['traitement'])){
	
echo '<script>alert("'.$_POST['traitement'].'");</script>' ;
		$ecart->traiter($_POST['traitement']);
		
		header("Location: /route/ecarts.php");
} 

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/modif_ecart.tpl.html");
	else
	include("connexion.php");

?>
