<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';
include_once '../classes/class.phpmailer.php';
include_once '../classes/class.smtp.php';
include_once '../classes/type_ecart.class.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");


/* Récupération du nouvel id de stage */
$idTypeActuel = TypeEcart::getNewTypeEcartNumber();

$site = new Site($_SESSION['site']);
$typeécarts = TypeEcart::getAllFromSite($site->_id);

if (isset($_POST['nom'])){
	$newtypeécart= TypeEcart::creer($idTypeActuel,$_POST['nom'],$site);
		
	
	header("Location: creerTypeEcart.php");
}




/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/creer_typeecart.tpl.html");
else
	include("connexion.php");

?>
