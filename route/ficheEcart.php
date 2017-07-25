<?php
include_once '../config.php';
include_once '../classes/ecart.class.php';

/*
Démarrage de session
*/
session_cache_limiter('private, must-revalidate');
session_start();

$ecart = new Ecart($_GET['id']);


$site = new Site($_SESSION['site']);

try
{
	/* Connexion à la BD*/
  	$bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	
	

  /*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" "){


	include("../tpl/pages/fiche_ecart.tpl.html");}
else
	include("connexion.php");
}

catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}

?>
