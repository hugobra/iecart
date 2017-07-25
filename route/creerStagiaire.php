<?php
include_once '../config.php';
include_once '../classes/stagiaire.class.php';
include_once '../classes/site.class.php';
include_once '../classes/service.class.php';
include_once '../classes/equipe.class.php';
include_once '../classes/tranche.class.php';
include_once '../classes/fonction.class.php';
include_once '../classes/rang.class.php';
include_once '../classes/request.class.php';

/*
Démarrage de session, récupération de la date du jour
*/
session_cache_limiter('private, must-revalidate');
session_start();
$date = date("d-m-Y");

if ($_SESSION['rang'] < Rangs::CONDUITE){
	header("Location: /route/stages.php?permission_denied");
}

$site = new Site($_SESSION['site']);

$services  = Service::getAllFromSite($site);
$equipes   = Equipe::getAllFromSite($site);
$tranches  = Tranche::getAllFromSite($site);
$fonctions = Fonction::getAllFromSite($site);

if(empty($services) || empty($equipes) || empty($tranches) || empty($fonctions)){
	echo"<script>alert('Aucun(e) service, equipe, tranche ou fonction n\'est configuré(e), contactez l\'admin site.');window.location='stagiaires.php';</script>";
}

if (isset($_POST['nom'], $_POST['prenom'], $_POST['service'], $_POST['equipe'], $_POST['tranche'], $_POST['fonctionActuelle'], $_POST['DUA'])){
	$stagiaire = new Stagiaire(null, $site, $_POST['nom'], $_POST['prenom'], $_POST['service'], $_POST['equipe'], $_POST['tranche'], $_POST['fonctionActuelle'], $_POST['nni'],$_POST['DUA']);
	
	if (!$stagiaire->hasProfileOnAnotherSite()){
		$idstagiaire = $stagiaire->creer();

		//Redirection vers la page du nouveau stagiaire
		echo"<script>window.location='ficheStagiaire.php?id=".$idstagiaire."'</script>";
	}else{
		$requests = Request::getAllPending($site);
			
			
		$alreadyPending = false;
		foreach ($requests as $request) {
			if ($request->_stagiaire->_id == $stagiaire->getId() && $site->_id == $request->_site->_id){
				header("Location: /route/creerStagiaire.php?request_already_pending");
				return;
			}
		}

		$idstagiaire = $stagiaire->askNewSite($site);
		echo"<script>alert('Votre demande a bien été effectuée, lorsque la demande sera validée, le profil sera disponible sur votre site.');window.location='stagiaires.php';</script>";
				
	}
}

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" "){
	include("../tpl/pages/creer_stagiaire.tpl.html");
}else{
	include("connexion.php");
}
?>
