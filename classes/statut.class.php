<?php
/**
* Classe de rang
*/
class Statut
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

		$rangQuery = $bdd->_connexion->Query("SELECT * FROM statut_ecart WHERE idStatut = " . $id);
		if (!$rangQuery){
			return false;
		}
		$rangQuery->setFetchMode(PDO::FETCH_OBJ);
		$rangFetch = $rangQuery->fetch();

		$this->_nom = $rangFetch->NomStatut;

		return true;
	}

	/**
	* Obtenir tous les rangs
	*/
	public static function getAll(){
		$rangs = array();

		$bdd = new BDD();

		$rangQuery = $bdd->_connexion->Query("SELECT * FROM statut_ecart");
		if (!$rangQuery){
			return false;
		}
		$rangQuery->setFetchMode(PDO::FETCH_OBJ);
		while($rangFetch = $rangQuery->fetch()){
			array_push($rangs, new rang($rangFetch->idStatut, $rangFetch->NomStatut));
		}

		return $rangs;
	}
}


?>