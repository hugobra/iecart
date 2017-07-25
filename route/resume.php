<?php
include_once '../config.php';
include_once '../classes/site.class.php';
include_once '../classes/stage.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
include_once '../classes/manager.class.php';
include_once '../classes/service.class.php';
include_once '../classes/equipe.class.php';
include_once '../classes/tranche.class.php';

session_cache_limiter('private, must-revalidate');
session_start();
/*
Démarrage de session
*/

$site = new Site($_SESSION['site']);

$stages = Stage::getAllFromSite($site);
$fonctions = Fonction::getAllFromSite($site);
if ($_SESSION['rang'] >= Rangs::FORMATEUR || $_SESSION['rang'] >= Rangs::CONDUITE ){
$services = Service::getAllFromSite($site);
$equipes = Equipe::getAllFromSite($site);
$tranches = Tranche::getAllFromSite($site);
}
if ($_SESSION['rang'] == Rangs::MANAGER1){
$manager = new Manager($_SESSION['nni']);
$services= array(new Service($manager->_service->_id));
$equipes = array(new Equipe($manager->_équipe->_id));
$tranches= array(new Tranche( $manager->_tranche->_id));
}
if ($_SESSION['rang'] == Rangs::MANAGER2){
$manager = new Manager($_SESSION['nni']);
$services=array( new Service($manager->_service->_id));
$equipes = Equipe::getAllFromSite($site);
$tranches = Tranche::getAllFromSite($site);	
}


/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
    include("../tpl/pages/resume.tpl.html");
else	
    include("connexion.php");



?>
