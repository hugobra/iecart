<?php
include_once '../config.php';
include_once '../classes/site.class.php';

/*
Démarrage des sessions, et suppression des anciennes sessions pour ne pas garder de variables
*/

session_cache_limiter('private, must-revalidate');
session_start();
session_unset();
session_destroy();

session_cache_limiter('private, must-revalidate');
session_start();

$sites = Site::getAll();

include("../tpl/forms/connexion.tpl.html");
include("../tpl/layout/footer.html");


if(isset($_GET['error_log'])){
	echo "<script>alert(\"Mot De Passe Erroné\");</script>";
}

if(isset($_GET['no_profil'])){
	echo "<script>alert(\"Vous n'avez pas de profil enregistré. Si vous êtes formateur, demandez à l'administrateur de votre site de vous ajouter en tant que tel sur iFAP\");</script>";
}

if (preg_match("/MSIE/", getenv("HTTP_USER_AGENT"))){
	echo "<script>alert(\"Attention, vous utilisez Internet Explorer, pour utiliser cette application, il est conseillé d'utiliser Firefox\");</script>";
}

?>
