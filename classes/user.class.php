<?php
include_once 'bdd.class.php';
include_once 'site.class.php';
include_once 'rang.class.php';

/**
* Classe abstraite dÃ©finissant un utilisateur
*
* Formateur, Admin, SuperAdmin, etc dÃ©rivent de cette classe
*/
abstract class User
{
	public $_id;
	public $_nni;
	public $_nom;
	public $_prenom;
	public $_mail;
	public $_rang;
	public $_site;
	
	


	function __construct($id, $site = null, $nom = null, $prenom = null, $nni = null,$mail = null, $rang = null){
		$this->_id = $id;
		if (!empty($site) && !empty($nom) && !empty($prenom) && !empty($mail) && !empty($rang) ){
			$this->_site     = $site;
			$this->_nom      = $nom;
			$this->_prenom   = $prenom;
			$this->_mail  = $mail;
			$this->_rang   = $rang;
			
			$this->_nni      = $nni;
			
		}else{
			$this->getInfos();
		}
	}

	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		//$userQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE idStagiaire = " . $id);
		$userQuery = $bdd->_connexion->Query("SELECT * FROM user WHERE idUser = " . $id);
		if (!$userQuery){
			return false;
		}

		$userQuery->setFetchMode(PDO::FETCH_OBJ);
		$userFetch = $userQuery->fetch();
		$this->_nni      = $userFetch->NNI;
		
		$this->_nom      = $userFetch->Nom;
		$this->_prenom   = $userFetch->Prenom;
		$this->_mail  = $userFetch->Mail;
		$this->_rang     = $userFetch->Rang;
		$this->_site     = $userFetch->Site;
		
	

		return true;
	}
}

/**
* Classe permettant la management des utilisateurs
*/
class UserManager extends User
{
function __construct(){
		// RÃ©cupÃ¨re les paramtres et appelle le constrcuteur parent
		$args = func_get_args();
		call_user_func_array(array($this, 'parent::__construct'), $args);
	}

	/**
	* Permet d'obtenir les informations concernant un utilisateur
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		//$userQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE idStagiaire = " . $id);
		$userQuery = $bdd->_connexion->Query("SELECT * FROM user WHERE idUser = " . $id);
		if (!$userQuery){
			return false;
		}

		$userQuery->setFetchMode(PDO::FETCH_OBJ);
		$userFetch = $userQuery->fetch();
		$this->_nni      = $userFetch->NNI;
		
		$this->_nom      = $userFetch->Nom;
		$this->_prenom   = $userFetch->Prenom;
		$this->_mail  = $userFetch->Mail;
		$this->_rang     = $userFetch->Rang;
		$this->_site     = $userFetch->Site;
		

		return true;
	}
	/**
	* Obtenir un objet de type stagiaire
	*/
	public static function getStagiaire($id){
		$bdd = new BDD();

		$idQuote = $bdd->_connexion->Quote($id);

		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM stagiaire st JOIN equipes e ON (e.idEquipe = st.equipe) JOIN tranches t ON (t.idTranche = st.tranche) JOIN services s ON(st.service = s.idService) JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) WHERE idStagiaire = {$idQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();

		return new Stagiaire($stagiaireFetch->idStagiaire, new Site($stagiaireFetch->idSite), $stagiaireFetch->nom, $stagiaireFetch->prenom, new Service($stagiaireFetch->idService, $stagiaireFetch->nomService), new Equipe($stagiaireFetch->idEquipe, $stagiaireFetch->nomEquipe), $stagiaireFetch->tranche, $stagiaireFetch->fonctionActuelle);
	}
	
	/**
	* Obtenir un objet de type agent
	*/
	public static function getAgentfromId($id){
		$bdd = new BDD();

		$idQuote = $bdd->_connexion->Quote($id);

		$agentQuery = $bdd->_connexion->query("SELECT * FROM users WHERE idUser = {$idQuote}");
		$agentQuery->setFetchMode(PDO::FETCH_OBJ);
		$agentFetch = $agentQuery->fetch();

		return new UserManager($agentFetch->idUser, $agentFetch->nni, $agentFetch->prenom, $agentFetch->nom, $agentFetch->idRang, new Site($agentFetch->idSite), $agentFetch->idService, $agentFetch->idEquipe, $agentFetch->idTranche);
	}

	/**
	* Obtenir un objet de type stagiaire
	*/
	public static function getUserFromNNI($nni){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);

		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();

		if(!isset($stagiaireFetch->idUser)){
			return false;
		}

		return new UserManager($stagiaireFetch->idUser);
	}

	/**
	* Obtenir tous les stagiaires d'un site
	*/
	public static function getAllStagiaireFromSite($site){
		$stagiaires = array();

		$bdd = new BDD();

		$siteQuote = $bdd->_connexion->Quote($site);

		//$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE idSite = {$siteQuote}");
		//$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire st JOIN equipes e ON (e.idEquipe = st.equipe) JOIN tranches t ON (t.idTranche = st.tranche) JOIN services s ON(st.service = s.idService) JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) WHERE idSite = {$siteQuote}");
		//$stagiaireQuery=$bdd->_connexion->query("SELECT DISTINCT * FROM stagiaire st JOIN equipes e ON (e.idEquipe = st.equipe) JOIN tranches t ON (t.idTranche = st.tranche) JOIN services s ON(st.service = s.idService) JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) JOIN stagiairesite ss ON (ss.idStagiaire = st.idStagiaire) WHERE st.idSite = {$siteQuote} OR ss.idSite = {$siteQuote}");
		$stagiaireQuery=$bdd->_connexion->query("SELECT DISTINCT * FROM stagiaire st LEFT JOIN equipes e ON (e.idEquipe = st.equipe) LEFT JOIN tranches t ON (t.idTranche = st.tranche) LEFT JOIN services s ON(st.service = s.idService) LEFT JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) LEFT JOIN site ON (site.idSite = {$siteQuote}) WHERE st.idSite = {$siteQuote} OR st.idStagiaire IN (SELECT idStagiaire FROM stagiairesite ss WHERE ss.idSite = {$siteQuote})");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			//array_push($stagiaires, new Stagiaire($stagiaireFetch->idStagiaire, new Site($stagiaireFetch->idSite), $stagiaireFetch->nom, $stagiaireFetch->prenom, new Service($stagiaireFetch->idService, $stagiaireFetch->nomService), new Equipe($stagiaireFetch->idEquipe, $stagiaireFetch->nomEquipe), new Tranche($stagiaireFetch->idTranche, $stagiaireFetch->nomTranche), new Fonction($stagiaireFetch->idFonction, $stagiaireFetch->nomFonction), $stagiaireFetch->nni));
			array_push($stagiaires, new Stagiaire($stagiaireFetch->idStagiaire, new Site($stagiaireFetch->idSite, $stagiaireFetch->nomSite), $stagiaireFetch->nom, $stagiaireFetch->prenom, new Service($stagiaireFetch->idService, $stagiaireFetch->nomService), new Equipe($stagiaireFetch->idEquipe, $stagiaireFetch->nomEquipe), new Tranche($stagiaireFetch->idTranche, $stagiaireFetch->nomTranche), new Fonction($stagiaireFetch->idFonction, $stagiaireFetch->nomFonction), $stagiaireFetch->nni, $stagiaireFetch->DUA));
		}

		return $stagiaires;
	}

	/**
	* Obtenir tous les stagiaires d'un site et d'une equipe
	*/
	public static function getAllStagiaireFromSiteAndEquipe($site, $equipe){
		$stagiaires = array();

		$bdd = new BDD();

		$siteQuote = $bdd->_connexion->Quote($site);
		$equipeQuote = $bdd->_connexion->Quote($equipe);

		//$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE idSite = {$siteQuote}");
		//$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire st JOIN equipes e ON (e.idEquipe = st.equipe) JOIN tranches t ON (t.idTranche = st.tranche) JOIN services s ON(st.service = s.idService) JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) WHERE idSite = {$siteQuote} AND idEquipe = {$equipeQuote}");
		$stagiaireQuery=$bdd->_connexion->query("SELECT DISTINCT * FROM stagiaire st LEFT JOIN equipes e ON (e.idEquipe = st.equipe) LEFT JOIN tranches t ON (t.idTranche = st.tranche) LEFT JOIN services s ON(st.service = s.idService) LEFT JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) JOIN stagiairesite ss ON (ss.idStagiaire = st.idStagiaire) LEFT JOIN site ON (site.idSite = {$siteQuote}) WHERE (st.idSite = {$siteQuote} OR ss.idSite = {$siteQuote}) AND idEquipe = {$equipeQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($stagiaires, new Stagiaire($stagiaireFetch->idStagiaire, new Site($stagiaireFetch->idSite, $stagiaireFetch->nomSite), $stagiaireFetch->nom, $stagiaireFetch->prenom, new Service($stagiaireFetch->idService, $stagiaireFetch->nomService), new Equipe($stagiaireFetch->idEquipe, $stagiaireFetch->nomEquipe), new Tranche($stagiaireFetch->idTranche, $stagiaireFetch->nomTranche), new Fonction($stagiaireFetch->idFonction, $stagiaireFetch->nomFonction), $stagiaireFetch->nni, $stagiaireFetch->DUA));
		}

		return $stagiaires;
	}

	/**
	* Supprimer un stagiaire (Eliminer les stagiaires, c'est mal !)
	*/
	public static function deleteStagiaire($id){
		$bdd = new BDD();

		$idQuote = $bdd->_connexion->Quote($id);

		$bdd->_connexion->query("DELETE FROM stagiaire WHERE idStagiaire= {$idQuote}");

		return true;
	}

	/**
	* Permet d'obtenir le rang en fonction du NNI
	*/
	public static function getRangFromNNI($nni, $site){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		$siteQuote = $bdd->_connexion->Quote($site);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote} AND (Site = {$siteQuote} OR Rang = 6)");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();

		return $stagiaireFetch->Rang;
	}

	/**
	* Permet mettre Ã  jour le nomd e la eprsonne en base (si marriÃ©e par exemple)
	*/
	public static function updateName($nni, $cn){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		
		$nomTab = explode(" ", $cn);
		$sizeNomTab = count($nomTab);

		$prenom = "";

		foreach ($nomTab as $key => $nomPart) {
			if ($key < $sizeNomTab - 1){
				$prenom .= ucfirst($nomPart . " ");
			}else{
				$nom = strtoupper($nomPart);
			}
		}

		$prenomQuote = $bdd->_connexion->Quote($prenom);
		$nomQuote = $bdd->_connexion->Quote($nom);

		$bdd->_connexion->query("UPDATE users SET nom = {$nomQuote}, prenom = {$prenomQuote} WHERE nni = {$nniQuote}");

		return true;
	}

	/**
	* Permet d'obtenir le nom en fonction du NNI
	*/
	public static function getNamefromNNI($nni){
		if (DB_NAME != "ecart"){
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
				$ldapAuth->getUserDn($nni);

				$cn = $ldapAuth->getCN();

				return $cn;
			} catch (LdapException $e) {
				echo $e->getMessage();
			}
		}else{
			$bdd = new BDD();

			$nniQuote = $bdd->_connexion->Quote($nni);
			$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote}");
			$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
			$stagiaireFetch = $stagiaireQuery->fetch();
			if (empty($stagiaireFetch->prenom)){
				$stagiaireQuery2=$bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote}");
				$stagiaireQuery2->setFetchMode(PDO::FETCH_OBJ);
				$stagiaireFetch2 = $stagiaireQuery2->fetch();
				if (!$stagiaireFetch2){
					return false;
				}else{
					return $stagiaireFetch2->Prenom . " " . strtoupper($stagiaireFetch2->Nom);
				}
			}

			return $stagiaireFetch->Prenom . " " . strtoupper($stagiaireFetch->Nom);
		}
	}

	/**
	* Permet d'obtenir l'equipe en fonction du NNI
	*/
	public static function getEquipeFromNNI($nni){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM users WHERE nni = {$nniQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch)
			return false;

		return $stagiaireFetch->idEquipe;
	}
	
	

	/**
	* Permet d'obtenir l'id d'un stagiaire en fonction du NNI
	
	*/
	
	public static function getStagiaireFromNNIAndSite($nni, $site){
		$bdd = new BDD();
   
   /* if (empty($nni)){
                               return false;
                }
*/
		$nniQuote = $bdd->_connexion->Quote($nni);
		$siteQuote = $bdd->_connexion->Quote($site);

		// $stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
   
    $stagiaireQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE nni != '' AND nni = {$nniQuote} AND idSite = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch)
			return false;

		return $stagiaireFetch->idStagiaire;
	}
	
	
	
	
	/**
	* Permet de changer le rang de l'utilisateur grace Ã  son NNI
	*/
	
	public static function changeRangFromNNI($nni, $rang){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		$rangQuote = $bdd->_connexion->Quote($rang);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM users WHERE nni = {$nniQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch){
			$bdd->_connexion->query("INSERT INTO users (nni, idRang) VALUES({$nniQuote}, {$rangQuote})");
		}
		else{
			$bdd->_connexion->query("UPDATE users SET idRang = {$rangQuote} WHERE nni = {$nniQuote}");
		}
	}

	/**
	*Permet de changer le rang d'un utilisateur grace Ã  son NNI pour un seul site et pour un MPL de lui attribuer ces stagiaires
	*/
	public static function changeRangFromNNIAndSiteavecfonctionMPL($nni, $rang, $site, $service, $Ã©quipe ,$tranche){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		$rangQuote = $bdd->_connexion->Quote($rang);
		$siteQuote = $bdd->_connexion->Quote($site);
		$service = $bdd->_connexion->Quote($service);
		$Ã©quipe = $bdd->_connexion->Quote($Ã©quipe);
		$tranche = $bdd->_connexion->Quote($tranche);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM users WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch){
			$bdd->_connexion->query("INSERT INTO users (nni, idRang, idSite) VALUES({$nniQuote}, {$rangQuote}, {$siteQuote})");
		}
		else{
			$bdd->_connexion->query("UPDATE users SET idRang = {$rangQuote} WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
		}
		
$stagiaireQuery2 = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE service = {$service} AND equipe={$Ã©quipe} AND tranche={$tranche}");
$stagiaireQuery2->setFetchMode(PDO::FETCH_ASSOC);
	while ($stagiaireFetch2 = $stagiaireQuery2->fetch()){
		
		
		if($nniQuote!=$stagiaireFetch2['nni']){
	$bdd->_connexion->Query("INSERT INTO managerstaff (nniManager, idStagiaire, idSite) VALUES({$nniQuote}, ".$stagiaireFetch2['idStagiaire'].", ".$stagiaireFetch2['idSite'].") ON DUPLICATE KEY UPDATE id=id");
		}
	}
	
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
			$ldapAuth->getUserDn($nni);

			$cn = $ldapAuth->getCN();

			UserManager::updateName($nni, $cn);
		} catch (LdapException $e) {
			echo $e->getMessage();
		}
	}

	/**
	*Permet d'ajouter un MPL en tant que users et de lui attribuer Ã©quipe service et tranche
	*/
	public static function ajouterMPL($nni, $site, $service, $Ã©quipe ,$tranche){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		
		$siteQuote = $bdd->_connexion->Quote($site);
		$service = $bdd->_connexion->Quote($service);
		$Ã©quipe = $bdd->_connexion->Quote($Ã©quipe);
		$tranche = $bdd->_connexion->Quote($tranche);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM users WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch){
			$bdd->_connexion->query("INSERT INTO users (nni, idRang, idSite,idEquipe,idTranche,idService) VALUES({$nniQuote},4, {$siteQuote},{$Ã©quipe},{$tranche},{$service})");
		}
		else{
			$bdd->_connexion->query("UPDATE users SET idRang =4 ,idSite = {$siteQuote},idEquipe={$Ã©quipe},idTranche={$tranche},idService={$service} WHERE nni = {$nniQuote} AND idSite = {$siteQuote} ");
		}

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
			$ldapAuth->getUserDn($nni);

			$cn = $ldapAuth->getCN();

			UserManager::updateName($nni, $cn);
		} catch (LdapException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	*Permet d'ajouter un CDS en tant que users et de lui attribuer un service
	*/
	public static function ajouterCDS($nni, $site, $service){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		
		$siteQuote = $bdd->_connexion->Quote($site);
		$service = $bdd->_connexion->Quote($service);
		

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM users WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch){
			$bdd->_connexion->query("INSERT INTO users (nni, idRang, idSite,idService) VALUES({$nniQuote},5, {$siteQuote},{$service})");
		}
		else{
			$bdd->_connexion->query("UPDATE users SET idRang = 5,idSite = {$siteQuote}, idService={$service} WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");
		}

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
			$ldapAuth->getUserDn($nni);

			$cn = $ldapAuth->getCN();

			UserManager::updateName($nni, $cn);
		} catch (LdapException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	* Permet de changer le rang d'un utilisateur grace Ã  son NNI pour un seul site
	*/
	public static function changeRangFromNNIAndSite($nni, $rang, $site,$mail){
		$bdd = new BDD();

		$nniQuote = $bdd->_connexion->Quote($nni);
		$rangQuote = $bdd->_connexion->Quote($rang);
		$siteQuote = $bdd->_connexion->Quote($site);
		$mail = $bdd->_connexion->Quote($mail);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM user WHERE NNI = {$nniQuote} AND Site = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();
		if (!$stagiaireFetch){
			$bdd->_connexion->query("INSERT INTO user (NNI, Rang, Site, Mail) VALUES({$nniQuote}, {$rangQuote}, {$siteQuote},{$mail})");
		}
		else{
			$bdd->_connexion->query("UPDATE user SET Rang = {$rangQuote} WHERE NNI = {$nniQuote} AND Site = {$siteQuote} AND Mail={$mail} ");
		}

		

	
	
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
			$ldapAuth->getUserDn($nni);

			$cn = $ldapAuth->getCN();

			UserManager::updateName($nni, $cn);
		} catch (LdapException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	* Permet d'obtenir tous les agents enregistrÃ©s
	*/
	public static function getAllAgents(){
		$bdd = new BDD();

		$users = array();

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM user");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($users, $stagiaireFetch);
		}

		return $users;
	}


	/**
	* Permet d'obtenir tous les agents enregistrÃ©s d'un site
	*/
	public static function getAllAgentsFromSite($idSite){
		$bdd = new BDD();

		$users = array();

		$siteQuote = $bdd->_connexion->Quote($idSite);

		$stagiaireQuery=$bdd->_connexion->query("SELECT * FROM user WHERE Site = {$siteQuote}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($users, new UserManager($stagiaireFetch->idUser));
		}

		return $users;
	}

	/**
	* Supprimer les accÃ¨s d'un agent sur un site
	*/
	public static function deleteAgentFromNNIAndSite($nni, $idSite){
		$bdd = new BDD();

		$siteQuote = $bdd->_connexion->Quote($idSite);
		$nniQuote  = $bdd->_connexion->Quote($nni);

		$bdd->_connexion->query("DELETE FROM users WHERE nni = {$nniQuote} AND idSite = {$siteQuote}");

		return true;
	}

	/**
	* Verifier que le nni correspond a un agent sur le site
	*/
	public static function isNniOnSite($nni, $idSite){
		$agents = UserManager::getAllAgentsFromSite($idSite);

		foreach ($agents as $agent) {
			if ($agent->nni == $nni){
				return true;
			}
		}

		return false;
	}
}
?>