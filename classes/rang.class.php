<?php
/**
* Classe de rang
*/
class Rang
{
	public $_id;
	public $_nom;

	function __construct($id, $nom = null){
		$this->_id  = $id;
		$this->_nom = $nom;

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

		$rangQuery = $bdd->_connexion->Query("SELECT * FROM rangs WHERE idRang = " . $id);
		if (!$rangQuery){
			return false;
		}
		$rangQuery->setFetchMode(PDO::FETCH_OBJ);
		$rangFetch = $rangQuery->fetch();

		$this->_nom = $rangFetch->NomRang;

		return true;
	}

	/**
	* Obtenir tous les rangs
	*/
	public static function getAll(){
		$rangs = array();

		$bdd = new BDD();

		$rangQuery = $bdd->_connexion->Query("SELECT * FROM rangs");
		if (!$rangQuery){
			return false;
		}
		$rangQuery->setFetchMode(PDO::FETCH_OBJ);
		while($rangFetch = $rangQuery->fetch()){
			array_push($rangs, new rang($rangFetch->idRang, $rangFetch->NomRang));
		}

		return $rangs;
	}
}

abstract class Rangs
{
	const AUCUN       = 0;
	const AGENT   = 3;
	const MANAGER1    = 4;
	const ADMIN =5;
	const SUPERADMIN =6;
	
}
?>