<?php
include_once 'stage.class.php';
include_once 'fonction.class.php';
include_once 'objectif.class.php';

/**
* Classe d'OPS
*/
class OPS
{
	public $_id;
	public $_stage;
	public $_OPG;
	public $_fonction;
	public $_nom;
	
	
	
	function __construct($id, $stage = null, $OPG = null , $fonction = null, $nom = null){
		$this->_id = $id;
		$this->_stage = $stage;
		$this->_fonction = $fonction;
		$this->_nom = $nom;
		$this->_OPG = $OPG;
		if (empty($stage) && empty($fonction)){
			$this->getInfos();
		}

		
	}

	/**
	* Permet d'obtenir les informations concernant un objectif
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idOPS = " . $id);
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		$objectifFetch = $objectifQuery->fetch();

		$this->_stage = new Stage($objectifFetch->idStage);
		$this->_fonction = new Fonction($objectifFetch->idFonction);
		$this->_nom =$objectifFetch->nomOPS;
		$this->_OPG = New Objectif($objectifFetch->idObjectif);

		return true;
	}

	/**
	* Obtenir tous les objectifs selon la fonction qu'occupe le stagiaire
	*/
	public static function getAllFromFonction($fonction){
		$objectifs = array();

		$bdd = new BDD();

		$fonction = $bdd->_connexion->Quote($fonction->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idFonction = {$fonction}");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_unshift($objectifs, new OPS($objectifFetch->idOPS, new Stage($objectifFetch->idStage), $fonction, $objectifFetch->nomOPS));
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

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idStage = {$stage} AND idFonction = {$fonction}");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_unshift($objectifs, new OPS($objectifFetch->idOPS, $stage,$objectifFetch->idObjectif, $fonction, $objectifFetch->nomOPS));
		}

		return $objectifs;
	}

	/**
	* Modifier le nom d'un ops
	*/
	public function modifierOPS($nom){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);
		$nom = $bdd->_connexion->Quote($nom);

		$bdd->_connexion->Query("UPDATE ops SET nomOPS = {$nom} WHERE idOPS= {$id}");
	}
}
?>