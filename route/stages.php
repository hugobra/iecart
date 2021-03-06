<?php
include_once '../config.php';
include_once '../classes/site.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/rang.class.php';

session_cache_limiter('private, must-revalidate');
session_start();
/*
Démarrage de session
*/

$site = new Site($_SESSION['site']);
$stages = Stage::getAllFromSite($site);

if (isset($_GET['idSuppr'])){
	if ($_SESSION['rang'] >= Rangs::ADMINSITE){
		$stage = new Stage($_GET['idSuppr']);

		$stage->supprimer();

		echo"<script>window.location='stages.php'</script>";
	}
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
    include("../tpl/pages/stages.tpl.html");
else	
    include("connexion.php");



?>
