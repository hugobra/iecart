<?php
include_once '../config.php';
include_once '../classes/site.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/fap.class.php';


session_cache_limiter('private, must-revalidate');
session_start();
/*
Démarrage de session
*/

$site = new Site($_SESSION['site']);
$fonctions = Fonction::getAllFromSite($site);
$stages = Stage::getAllFromSite($site);
//ini_set('max_execution_time', 300);
if(isset($_POST['stageFiltre'])&&isset($_POST['fonctionFiltre'])&&isset($_POST['serviceFiltre'])&&isset($_POST['equipeFiltre'])&&isset($_POST['trancheFiltre'])){
$faps = Fap::getAllBetweenDatesFromSiteWithFiltersaveclistes($_POST['dateDebut'], $_POST['dateFin'], $site, $_POST['stageFiltre'], $_POST['fonctionFiltre'],$_POST['serviceFiltre'],$_POST['equipeFiltre'],$_POST['trancheFiltre']);
/*foreach($_POST['stageFiltre'] as $stage){
	foreach($_POST['fonctionFiltre'] as $fonction){
		foreach($_POST['serviceFiltre'] as $service){
			foreach($_POST['equipeFiltre'] as $equipe){
				foreach($_POST['trancheFiltre'] as $tranche){
	
	if(isset($faps)){
		$listFaps=$faps;
	$faps = Fap::getAllBetweenDatesFromSiteWithFiltersmulti($listFaps, $_POST['dateDebut'], $_POST['dateFin'], $site, $stage, $fonction,$service,$equipe,$tranche);
			
	} else {
		$faps = Fap::getAllBetweenDatesFromSiteWithFilters($_POST['dateDebut'], $_POST['dateFin'], $site, $stage, $fonction,$service,$equipe,$tranche);
		
	}
	
}}}}}*/}




if (isset($_POST['dateDebut'], $_POST['dateFin'])){
	setcookie("dateDebutFilterResume", $_POST['dateDebut'], time()+3600);
	setcookie("dateFinFilterResume", $_POST['dateFin'], time()+3600);
	/*setcookie("stageFiltreFilterResume", $_POST['stageFiltre'], time()+3600);
	setcookie("fonctionFiltreFilterResume", $_POST['fonctionFiltre'], time()+3600);*/
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" " && isset($_POST['s1']) && $_POST['s1']=="parstage")
    include("../tpl/pages/resume_final.tpl.html");

else if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" " && isset($_POST['s1']) && $_POST['s1']=="brut")
	include("../tpl/pages/resume_brutes.tpl.html");
else	
    include("connexion.php");


?>
