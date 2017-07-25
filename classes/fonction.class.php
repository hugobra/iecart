<?php
/**
* Classe des fonctions
*/
class Fonction
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
	* Permet d'obtenir les informations concernant un fonction
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonctions WHERE idFonction = " . $id);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		$fonctionFetch = $fonctionQuery->fetch();

		$this->_nom = $fonctionFetch->nomFonction;
		$this->_site = new Site($fonctionFetch->idSite);

		return true;
	}

	/**
	* Obtenir toutes les fonctions
	*/
	public static function getAll(){
		$fonctions = array();

		$bdd = new BDD();

		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonctions");
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		while($fonctionFetch = $fonctionQuery->fetch()){
			array_push($fonctions, new fonction($fonctionFetch->idFonction, $fonctionFetch->nomFonction));
		}

		return $fonctions;
	}

	/**
	* Obtenir toutes les fonctions d'un site
	*/
	public static function getAllFromSite($site){
		$fonctions = array();

		$bdd = new BDD();
		
		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonctions WHERE idSite = " . $site->_id);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		while($fonctionFetch = $fonctionQuery->fetch()){
			array_push($fonctions, new fonction($fonctionFetch->idFonction, $fonctionFetch->nomFonction));
		}

		return $fonctions;
	}

	public function delete(){
		$bdd = new BDD();
		
		$bdd->_connexion->Query("UPDATE stagiaire SET fonctionActuelle = NULL WHERE fonctionActuelle = ".$this->_id);
		$bdd->_connexion->Query("DELETE FROM fonction WHERE nom = ".$this->_id);
		$bdd->_connexion->Query("SET foreign_key_checks = 0; DELETE FROM fonctions WHERE idFonction = ".$this->_id." AND idSite = " . $this->_site->_id);
	}

	public function create(){
		$bdd = new BDD();

		$nomQuote = $bdd->_connexion->Quote($this->_nom);
		$idSiteQuote = $bdd->_connexion->Quote($this->_site->_id);

		$bdd->_connexion->Query("INSERT INTO fonctions (nomFonction, idSite) VALUES({$nomQuote}, {$idSiteQuote})");
	}

	public static function getAllFromStage($stage){
		$fonctions = array();

		$bdd = new BDD();
		$stage = $bdd->_connexion->Quote($stage->_id);
		$fonctionQuery = $bdd->_connexion->Query("SELECT DISTINCT objectifsstage.idFonction FROM objectifsstage WHERE idStage = " . $stage);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		while($fonctionFetch = $fonctionQuery->fetch()){
			array_push($fonctions, new fonction($fonctionFetch->idFonction));
		}

		return $fonctions;
	}
	
	}
	
	class Fonctionhisto{
		public $_idFonction;
		public $_idStagiaire;
		public $_nom;
		public $_nomFonction;
		
		
		

	function __construct($idFonction, $idStagiaire = null, $nom = null, $nomFonction = null){
		$this->_idFonction  = $idFonction;
		$this->_nom = $nom;
		$this->_idStagiaire = $idStagiaire;
		$this->_nomFonction = $nomFonction;

		if (is_null($nom)){
			$this->getInfos2();
		}
	}

	/**
	* Permet d'obtenir les informations concernant un fonction
	*/
	public function getInfos2(){
		$bdd = new BDD();

		$idFonction = $bdd->_connexion->Quote($this->_idFonction);

		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonction WHERE idFonction = " . $idFonction);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		$fonctionFetch = $fonctionQuery->fetch();

		$this->_nom = $fonctionFetch->nom;
		$this->_idStagiaire = $fonctionFetch->idStagiaire;
		
		$nom = $bdd->_connexion->Quote($this->_nom);
		$nomFonction = $bdd->_connexion->Quote($this->_nomFonction);

		$fonctionQuery2 = $bdd->_connexion->Query("SELECT * FROM fonctions WHERE idFonction = " . $nom);
		if (!$fonctionQuery2){
			return false;
		}
		$fonctionQuery2->setFetchMode(PDO::FETCH_OBJ);
		$fonctionFetch2 = $fonctionQuery2->fetch();

		$this->_nomFonction = $fonctionFetch2->nomFonction;

		return true;
	}
	}
?>