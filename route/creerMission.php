<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';
include_once '../classes/class.phpmailer.php';
include_once '../classes/class.smtp.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");


/* Récupération du nouvel id de stage */
$idMissionActuel = Mission::getNewMissionNumber();

$site = new Site($_SESSION['site']);
$users=UserManager::getAllAgentsFromSite($site->_id);
$typeécarts= TypeEcart::getAllFromSite($site->_id);

if (isset($_POST['titre'], $_POST['description'],$_POST['responsable'])){
	$mission= Mission::creer($idMissionActuel,$_POST['titre'], $_POST['description'], $_POST['responsable'],$site);
		
	
	header("Location: ficheMission.php?id=" . $idMissionActuel);
}




/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/creer_mission.tpl.html");
else
	include("connexion.php");

?>
