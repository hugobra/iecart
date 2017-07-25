<?php
include_once 'user.class.php';
include_once 'site.class.php';
include_once 'rang.class.php';
include_once 'service.class.php';
include_once 'equipe.class.php';
include_once 'tranche.class.php';

/**
* Classe de manager, héritée de la classe abstraite user
*/
class Manager extends User
{
	function __construct($nni){
		// Récupère les paramtres et appelle le constrcuteur parent
		$this->_nni = $nni;

		$this->getInfos();
	}

	/**
	* Permet d'obtenir les informations concernant un utilisateur
	*/
	public function getInfos(){
		$bdd = new BDD();

		$nni = $bdd->_connexion->Quote($this->_nni);

		//$userQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE idStagiaire = " . $id);
		$userQuery = $bdd->_connexion->Query("SELECT * FROM users u WHERE u.nni = " . $nni);
		if (!$userQuery){
			return false;
		}
		$userQuery->setFetchMode(PDO::FETCH_OBJ);
		$userFetch = $userQuery->fetch();
		
		$this->_id		 = $userFetch->idUser;
		$this->_site     = new Site($userFetch->idSite);
		$this->_nom      = $userFetch->nom;
		$this->_prenom   = $userFetch->prenom;
		$this->_nni      = $userFetch->nni;
		$this->_service =  new Service($userFetch->idService);
		$this->_équipe =  new Equipe($userFetch->idEquipe);
		$this->_tranche =  new Tranche($userFetch->idTranche);
		$this->_rang =  new Rang($userFetch->idRang);

		return true;
	}

	/**
	* Obtenir tous les Manager 1 et 2 (MPL + CDS) d'un site
	*/
	public static function getAllFromSite($site){
		$bdd = new BDD();

		$managers = array();

		$siteQuote = $bdd->_connexion->Quote($site->_id);
		

		$managerQuery=$bdd->_connexion->query("SELECT nni FROM users WHERE (idRang = 4 OR idRang=5) AND idSite = {$siteQuote}");
		$managerQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($managerFetch = $managerQuery->fetch()){
			array_push($managers, new Manager($managerFetch->nni));
		}

		return $managers;
	}

	/**
	* Supprimer un manager un et ses agents
	*/
	public function delete(){
		$bdd = new BDD();

		$bdd->_connexion->query("DELETE FROM managerstaff WHERE nniManager = '" . $this->_nni ."'");
		$bdd->_connexion->query("DELETE FROM users WHERE nni = '".$this->_nni."' AND idSite = " . $this->_site->_id);
	}

	/**
	* Obtenir tous les stagiaires du manager
	*/
	public function getAllstagiaires(){
		$bdd = new BDD();

		$stagiaires = array();

		$stagiairesQuery=$bdd->_connexion->query("SELECT * FROM managerstaff WHERE nniManager = '" . $this->_nni . "'");
		$stagiairesQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiairesFetch = $stagiairesQuery->fetch()){
			array_push($stagiaires, new Stagiaire($stagiairesFetch->idStagiaire));
		}

		return $stagiaires;
	}
	
	/**
	* Obtenir tous les stagiaires du MPL nouvelle façon
	*/
	public function getAllstagiairesMPL(){
		$bdd = new BDD();

		$stagiaires = array();

		$stagiairesQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE equipe = '" . $this->_équipe->_id . "' AND tranche='" . $this->_tranche->_id . "' AND service='" . $this->_service->_id . "' ");
		$stagiairesQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiairesFetch = $stagiairesQuery->fetch()){
			array_push($stagiaires, new Stagiaire($stagiairesFetch->idStagiaire));
		}

		return $stagiaires;
	}

	/**
	* Obtenir tous les stagiaires du CDS nouvelle façon
	*/
	public function getAllstagiairesCDS(){
		$bdd = new BDD();

		$stagiaires = array();

		$stagiairesQuery=$bdd->_connexion->query("SELECT * FROM stagiaire WHERE service='" . $this->_service->_id . "' ");
		$stagiairesQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiairesFetch = $stagiairesQuery->fetch()){
			array_push($stagiaires, new Stagiaire($stagiairesFetch->idStagiaire));
		}

		return $stagiaires;
	}
	
	/**
	* Ajouter un agent au manager
	*/
	public function addAgent($stagiaire){
		$bdd = new BDD();

		$bdd->_connexion->query("INSERT INTO managerstaff (nniManager, idStagiaire, idSite) VALUES('" . $this->_nni . "', '".$stagiaire->_id."', '".$this->_site->_id."')");
		//var_dump("INSERT INTO managerstaff (nniManager, idStagiaire) VALUES('" . $this->_nni . "', {$nniQuote})");
	}

	/**
	* Supprimet un agent au manager
	*/
	public function deleteAgent($stagiaire){
		$bdd = new BDD();

		$bdd->_connexion->query("DELETE FROM managerstaff WHERE idStagiaire = '".$stagiaire->_id."' AND idSite = '".$this->_site->_id."'");
		//var_dump("DELETE FROM managerstaff WHERE idStagiaire = '".$stagiaire->_id."'");
	}

	/**
	* Changer le NNI du manager, permet de garder son équipe en enlevant les droits du manager actuel
	*/
	public function changeNNI($nni){
		$bdd = new BDD();
		
		$bdd->_connexion->query("UPDATE managerstaff SET nniManager = '" . $nni . "' WHERE nniManager = '".$this->_nni."' AND idSite = '".$this->_site->_id."'");

		$stagiaire = UserManager::getStagiaireFromNNI($nni);
		if($stagiaire){
			$bdd->_connexion->query("DELETE FROM managerstaff WHERE idStagiaire = '".$stagiaire->_id."' AND idSite = '".$this->_site->_id."'");
		}

		UserManager::deleteAgentFromNNIAndSite($this->_nni, $this->_site->_id);
		UserManager::changeRangFromNNIAndSite($nni, Rangs::MANAGER1, $this->_site->_id);
	}
}
?>