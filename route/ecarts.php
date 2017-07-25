<?php
include_once '../config.php';
include_once '../classes/user.class.php';
include_once '../classes/site.class.php';
include_once '../classes/ecart.class.php';
include_once '../classes/statut.class.php';


session_cache_limiter('private, must-revalidate');
session_start();
/*
Démarrage de session
*/

// Recupération de l'objet du site en question
if (isset($_POST['site'])){
	$site = new Site($_POST['site']);
}else if(isset($_SESSION['site'])){
	$site = new Site($_SESSION['site']);
}else{
	header('Location: connexion.php');
}
  
/* Suppression après confirmation du stagiaire*/
if(isset($_GET['idSuppr'])){
   	UserManager::deleteStagiaire($_GET['idSuppr']);
}
		
/* Vérification que le mot de passe est le bon pour le site (cf page connexion.tpl.html) */
if(isset($_POST['mdp'])){
	if ($_POST['ldap'] == "true"){
		include_once("../ldap/LdapAuth.php");

		// positionnement des options pour l'authentification
		// le minimum est de saisir les infos de connexion du compte applicatif (dn et password)
		$options = array(
			'servers' 			=> array(LDAP_HOST),
			'bind_dn'			=> LDAP_ROOTDN,
			'bind_pwd'			=> LDAP_PASSWORD,
			'use_ldaps'			=> true
		);
		try {
			$ldapAuth =new LdapAuth($options);
			$ldapAuth->authenticateUser($_POST['login'], $_POST['mdp']);

			$_SESSION['nni'] = $ldapAuth->getNNI();
			$_SESSION['cn'] = $ldapAuth->getCN();

			UserManager::updateName($_POST['login'], $_SESSION['cn']);
		} catch (LdapException $e) {
			echo $e->getMessage();
			if ($e->getErrorCode() == "password_incorrect"){
            	header('Location: connexion.php?error_log');
            }else{
                header('Location: connexion.php?error_unknow');
            }
		}
	}
	else{
		if (!$site->verifPass($_POST['mdp'])){
			header('Location: connexion.php?error_log');
		}

		$_SESSION['nni'] = $_POST['login'];
	}

	$_SESSION['rang'] = UserManager::getRangFromNNI($_POST['login'], $_POST['site']);
	$rang = new Rang($_SESSION['rang']);
	$_SESSION['nomRang'] = $rang->_nom;
	$_SESSION['nom2'] = UserManager::getNamefromNNI($_POST['login']);
	//$_SESSION['equipe'] = UserManager::getEquipeFromNNI($_POST['login']);
	$_SESSION['site'] = $_POST['site'];
	$_SESSION['idUser'] = UserManager::getUserFromNNI($_POST['login']);
}

/*Sélection des stages*/
if ($_SESSION['rang'] == Rangs::AGENT){
	$listecartsrecepteurs = Ecart::getAllFromSiteForUserRécepteur($_SESSION['idUser']);
	$listecartsemetteurs = Ecart::getAllFromSiteForUserEmetteur($_SESSION['idUser']);
	$listecarts = Ecart::getAllFromSite($_SESSION['site']);
	}else if ($_SESSION['rang'] > Rangs::AGENT){
	
	$listecarts = Ecart::getAllFromSite($_SESSION['site']);
}
/*else if ($_SESSION['rang'] == Rangs::MANAGER1){
	$manager = new Manager($_SESSION['nni']);
	$stagiaires = $manager->getAllstagiairesMPL();
	/*$stagiaireManager = UserManager::getStagiaireFromNNI($_SESSION['nni']);
	if($stagiaireManager){
		array_push($stagiaires, $stagiaireManager);
	}*/
	// $stagiaires = UserManager::getAllStagiaireFromSiteAndEquipe($_SESSION['site'], $_SESSION['equipe']);
/*}else{
	$stagiaire = UserManager::getStagiaireFromNNIAndSite($_SESSION['nni'], $_SESSION['site']);
	if ($stagiaire)
		header('Location: /route/ficheStagiaire.php?id=' . $stagiaire);
	else
		header('Location: /route/?no_profil');
}
//var_dump($stagiaires); //DEBUG: affichage de la liste des stagiaires

/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/ecarts.tpl.html");
else	
	include("connexion.php"); // Useless ? Mis par les anciens devs, a verifier
?>
