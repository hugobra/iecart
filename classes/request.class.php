<?php
/**
* classe gérant le request de fiche de profil
*/
class Request
{
	public $_id;
	public $_stagiaire;
	public $_site;
	
	function __construct($id){
		$this->_id = $id;

		$this->getInfos();
	}

	/**
	* Permet d'obtenir les informations concernant une request
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$serviceQuery = $bdd->_connexion->Query("SELECT * FROM profilerequest WHERE id = " . $id);
		if (!$serviceQuery){
			return false;
		}
		$serviceQuery->setFetchMode(PDO::FETCH_OBJ);
		$serviceFetch = $serviceQuery->fetch();

		$this->_stagiaire = new Stagiaire($serviceFetch->idStagiaire);
		$this->_site = new Site($serviceFetch->idSite);

		return true;
	}

	/**
	* Obtenir toutes les requetes en attente pour un site
	*/
	public static function getAllPending($site){
		$bdd = new BDD();

		$requests = array();

		$stagiaireQuery=$bdd->_connexion->query("SELECT pr.id FROM profilerequest pr JOIN stagiairesite ss ON(pr.idStagiaire = ss.idStagiaire) WHERE ss.idSite = " . $site->_id);
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($requests, new Request($stagiaireFetch->id));
		}

		return $requests;
	}

	/**
	* Obtenir le nombre de requetes en attente pour un site
	*/
	public static function getAllPendingCount($idSite){
		$bdd = new BDD();

		$stagiaireQuery=$bdd->_connexion->query("SELECT count(*) NBR FROM profilerequest pr JOIN stagiairesite ss ON(pr.idStagiaire = ss.idStagiaire) WHERE ss.idSite = " . $idSite);
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		$stagiaireFetch = $stagiaireQuery->fetch();

		return $stagiaireFetch->NBR;
	}

	/**
	* Accepter une demande
	*/
	public function accept(){
		$bdd = new BDD();

		$bdd->_connexion->query("DELETE FROM profilerequest WHERE id = " . $this->_id);
		$bdd->_connexion->query("INSERT INTO stagiairesite (idStagiaire, idSite) VALUES(".$this->_stagiaire->_id.", ".$this->_site->_id.")");
	}

	/**
	* Refuser une demande
	*/
	public function decline(){
		$bdd = new BDD();

		$bdd->_connexion->query("DELETE FROM profilerequest WHERE id = " . $this->_id);
	}
}
?>