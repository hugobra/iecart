<?php
include_once '../config.php';
include_once '../classes/stage.class.php';
include_once '../classes/site.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/user.class.php';
include_once '../classes/request.class.php';
include_once '../classes/manager.class.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

if ($_SESSION['rang'] < Rangs::ADMINSITE){
	header("Location: /route/stages.php?permission_denied");
}

$siteActuel = new Site($_SESSION['site']);

$manager = new Manager($_GET['manager']);
if($manager->_rang->_id==4){
$stagiaires = $manager->getAllstagiairesMPL();
}
if($manager->_rang->_id==5){
$stagiaires = $manager->getAllstagiairesCDS();
}
$services = Service::getAllFromSite($siteActuel);
$fonctions = Fonction::getAllFromSite($siteActuel);
$equipes = Equipe::getAllFromSite($siteActuel);
$tranches = Tranche::getAllFromSite($siteActuel);

if (isset($_GET['idSuppr'])){
	$stagiaire = UserManager::getStagiaireFromNNI($_GET['idSuppr']);

	if (!$stagiaire){
		header("Location: /route/admin_affect.php?manager=" . $_GET['manager'] . "&no_agent");
	}else{
		$manager->deleteAgent($stagiaire);

		header("Location: /route/admin_affect.php?manager=" . $_GET['manager']."#titretableau");
	}
}

if(isset($_POST['nniRole']) && $_POST['nniRole']!="" ){
	if ($_GET['manager'] == $_POST['nniRole']){
		header("Location: /route/admin_affect.php?manager=" . $_GET['manager'] . "&error_same_manager_nni");
	}else{
		$stagiaire = UserManager::getStagiaireFromNNI($_POST['nniRole']);

		if (!$stagiaire){
			header("Location: /route/admin_affect.php?manager=" . $_GET['manager'] . "&no_agent");
		}else{
			foreach ($stagiaires as $stagiaireATester) {
				if ($stagiaireATester->_id == $stagiaire->_id){
					header("Location: /route/admin_affect.php?manager=" . $_GET['manager'] . "&alreadyAgent");
					return;
				}
			}
			
			$manager->addAgent($stagiaire);

			header("Location: /route/admin_affect.php?manager=" . $_GET['manager']);
		}
	}
}

if(isset($_POST['nniManager'])){
	$manager->changeNNI($_POST['nniManager']);

	header("Location: /route/admin_affect.php?manager=" . $_POST['nniManager']);
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/admin_affect.tpl.html");
else
	include("connexion.php");

?>
