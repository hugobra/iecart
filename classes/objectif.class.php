<?php
include_once 'stage.class.php';
include_once 'fonction.class.php';

/**
* Classe d'objectif
*/
class Objectif
{
	public $_id;
	public $_stage;
	public $_fonction;
	public $_nom;
	public $_idFap;
	public $_results = array();
	
	function __construct($id, $stage = null, $fonction = null, $nom = null, $getResults = false, $idFap = null){
		$this->_id = $id;
		$this->_stage = $stage;
		$this->_fonction = $fonction;
		$this->_nom = $nom;
		if (empty($stage) && empty($fonction)){
			$this->getInfos();
		}

		if ($getResults){
			$this->_idFap = $idFap;
			$this->getResults();
		}
	}

	/**
	* Permet d'obtenir les informations concernant un objectif
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifsstage WHERE idObjectif = " . $id);
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		$objectifFetch = $objectifQuery->fetch();

		$this->_stage = new Stage($objectifFetch->idStage);
		$this->_fonction = new Fonction($objectifFetch->idFonction);
		$this->_nom = $objectifFetch->nomObjectif;

		return true;
	}

	/**
	* Obtenir tous les objectifs selon la fonction qu'occupe le stagiaire
	*/
	public static function getAllFromFonction($fonction){
		$objectifs = array();

		$bdd = new BDD();

		$fonction = $bdd->_connexion->Quote($fonction->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifsstage WHERE idFonction = {$fonction}");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_unshift($objectifs, new Objectif($objectifFetch->idObjectif, new Stage($objectifFetch->idStage), $fonction, $objectifFetch->nomObjectif));
		}

		return $objectifs;
	}

	/**
	* Obtenir tous les objectifs selon la fonction et le stage que suit le stagiaire
	*/
	public static function getAllFromStageAndFonction($stage, $fonction){
		$objectifs = array();

		$bdd = new BDD();

		$stage = $bdd->_connexion->Quote($stage->_id);
		$fonction = $bdd->_connexion->Quote($fonction->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifsstage WHERE idStage = {$stage} AND idFonction = {$fonction}");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_unshift($objectifs, new Objectif($objectifFetch->idObjectif, $stage, $fonction, $objectifFetch->nomObjectif));
		}

		return $objectifs;
	}

	/**
	* Obtenir les points forts et points à améliorer pour tous les objectifs de la FAP
	*/
	public function getResults(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);
		$idFap = $bdd->_connexion->Quote($this->_idFap);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifs WHERE idFap = {$idFap} AND objectif = " . $id);
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		$objectifFetch = $objectifQuery->fetch();

		$this->_results = array("PF" => $objectifFetch->pointFort, "PA" => $objectifFetch->pointAAmeliorer);
	}

	/**
	* Modifier le nom d'un objectif
	*/
	public function modifierNom($nom){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);
		$nom = $bdd->_connexion->Quote($nom);

		$bdd->_connexion->Query("UPDATE objectifsstage SET nomObjectif = {$nom} WHERE idObjectif = {$id}");
	}
}
?>