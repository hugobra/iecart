<?php
/**
* Classe des services
*/
class Service
{
	public $_id;
	public $_nom;
	public $_site;

	function __construct($id, $nom = null, $site = null){
		$this->_id  = $id;
		$this->_nom = $nom;
		$this->_site = $site;

		if (is_null($nom)){
			$this->getInfos();
		}
	}

	/**
	* Permet d'obtenir les informations concernant un service
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$serviceQuery = $bdd->_connexion->Query("SELECT * FROM services WHERE idService = " . $id);
		if (!$serviceQuery){
			return false;
		}
		$serviceQuery->setFetchMode(PDO::FETCH_OBJ);
		$serviceFetch = $serviceQuery->fetch();

		$this->_nom = $serviceFetch->nomService;
		$this->_site = new Site($serviceFetch->idSite);

		return true;
	}

	/**
	* Obtenir tous les services
	*/
	public static function getAll(){
		$services = array();

		$bdd = new BDD();

		$serviceQuery = $bdd->_connexion->Query("SELECT * FROM services");
		if (!$serviceQuery){
			return false;
		}
		$serviceQuery->setFetchMode(PDO::FETCH_OBJ);
		while($serviceFetch = $serviceQuery->fetch()){
			array_push($services, new Service($serviceFetch->idService, $serviceFetch->nomService));
		}

		return $services;
	}

	/**
	* Obtenir tous les services d'un site
	*/
	public static function getAllFromSite($site){
		$services = array();

		$bdd = new BDD();

		$serviceQuery = $bdd->_connexion->Query("SELECT * FROM services WHERE idSite = " . $site->_id);
		if (!$serviceQuery){
			return false;
		}
		$serviceQuery->setFetchMode(PDO::FETCH_OBJ);
		while($serviceFetch = $serviceQuery->fetch()){
			array_push($services, new Service($serviceFetch->idService, $serviceFetch->nomService));
		}

		return $services;
	}

	public function delete(){
		$bdd = new BDD();

		$bdd->_connexion->Query("UPDATE stagiaire SET service = NULL WHERE service = ".$this->_id);
		$bdd->_connexion->Query("DELETE FROM services WHERE idService = ".$this->_id." AND idSite = " . $this->_site->_id);
	}

	public function create(){
		$bdd = new BDD();

		$nomQuote = $bdd->_connexion->Quote($this->_nom);
		$idSiteQuote = $bdd->_connexion->Quote($this->_site->_id);

		$bdd->_connexion->Query("INSERT INTO services (nomService, idSite) VALUES({$nomQuote}, {$idSiteQuote})");
	}
}
?>