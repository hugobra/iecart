<?php
include_once '../config.php';
include_once '../classes/stagiaire.class.php';
include_once '../classes/site.class.php';
include_once '../classes/service.class.php';
include_once '../classes/equipe.class.php';
include_once '../classes/tranche.class.php';
include_once '../classes/fonction.class.php';
include_once '../classes/rang.class.php';

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("d-m-Y");

if ($_SESSION['rang'] < Rangs::CONDUITE){
	header("Location: /route/stages.php?permission_denied");
}

/*
Démarrage de session, récupération de la date du jour
*/

$site = new Site($_SESSION['site']);
$stagiaire = new Stagiaire($_GET['id']);

$services  = Service::getAllFromSite($site);
$equipes   = Equipe::getAllFromSite($site);
$tranches  = Tranche::getAllFromSite($site);
$fonctions = Fonction::getAllFromSite($site);

if (isset($_POST['nom'], $_POST['prenom'], $_POST['fonctionActuelle'], $_POST['equipe'], $_POST['tranche'], $_POST['service'],$_POST['DUA'])){
	$stagiaire->modifier($_POST['nom'], $_POST['prenom'], $_POST['service'], $_POST['equipe'], $_POST['tranche'], $_POST['fonctionActuelle'], $_POST['nni'], $_POST['DUA']);

	if ($_POST['fonctionActuelle'] != $stagiaire->_fonction->_id){
		$stagiaire->addFonction($_POST['fonctionActuelle']);
	}

	header("Location: ficheStagiaire.php?id=".$stagiaire->_id);
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" "){
	include("../tpl/pages/modifier_stagiaire.tpl.html");

}
else
	include("connexion.php");
?>
