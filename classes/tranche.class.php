<?php
/**
* Classe des tranches
*/
class Tranche
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
	* Permet d'obtenir les informations concernant un tranche
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$trancheQuery = $bdd->_connexion->Query("SELECT * FROM tranches WHERE idTranche = " . $id);
		if (!$trancheQuery){
			return false;
		}
		$trancheQuery->setFetchMode(PDO::FETCH_OBJ);
		$trancheFetch = $trancheQuery->fetch();

		$this->_nom = $trancheFetch->nomTranche;
		$this->_site = new Site($trancheFetch->idSite);

		return true;
	}

	/**
	* Permet d'obtenir toutes les tranches
	*/
	public static function getAll(){
		$tranches = array();

		$bdd = new BDD();

		$trancheQuery = $bdd->_connexion->Query("SELECT * FROM tranches");
		if (!$trancheQuery){
			return false;
		}
		$trancheQuery->setFetchMode(PDO::FETCH_OBJ);
		while($trancheFetch = $trancheQuery->fetch()){
			array_push($tranches, new tranche($trancheFetch->idTranche, $trancheFetch->nomTranche));
		}

		return $tranches;
	}

	/**
	* Permet d'obtenir toutes les tranches d'un site
	*/
	public static function getAllFromSite($site){
		$tranches = array();

		$bdd = new BDD();

		$trancheQuery = $bdd->_connexion->Query("SELECT * FROM tranches WHERE idSite = " . $site->_id);
		if (!$trancheQuery){
			return false;
		}
		$trancheQuery->setFetchMode(PDO::FETCH_OBJ);
		while($trancheFetch = $trancheQuery->fetch()){
			array_push($tranches, new tranche($trancheFetch->idTranche, $trancheFetch->nomTranche));
		}

		return $tranches;
	}

	public function delete(){
		$bdd = new BDD();

		$bdd->_connexion->Query("UPDATE stagiaire SET tranche = NULL WHERE tranche = ".$this->_id);
		$bdd->_connexion->Query("DELETE FROM tranches WHERE idTranche = ".$this->_id." AND idSite = " . $this->_site->_id);
	}

	public function create(){
		$bdd = new BDD();

		$nomQuote = $bdd->_connexion->Quote($this->_nom);
		$idSiteQuote = $bdd->_connexion->Quote($this->_site->_id);

		$bdd->_connexion->Query("INSERT INTO tranches (nomTranche, idSite) VALUES({$nomQuote}, {$idSiteQuote})");
	}
}
?>