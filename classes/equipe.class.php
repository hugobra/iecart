<?php
/**
* Classe des equipes
*/
class Equipe
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
	* Permet d'obtenir les informations concernant un equipe
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$equipeQuery = $bdd->_connexion->Query("SELECT * FROM equipes WHERE idEquipe = " . $id);
		if (!$equipeQuery){
			return false;
		}
		$equipeQuery->setFetchMode(PDO::FETCH_OBJ);
		$equipeFetch = $equipeQuery->fetch();

		$this->_nom = $equipeFetch->nomEquipe;
		$this->_site = new Site($equipeFetch->idSite);

		return true;
	}

	/**
	* Obtenir toutes les équipes
	*/
	public static function getAll(){
		$equipes = array();

		$bdd = new BDD();

		$equipeQuery = $bdd->_connexion->Query("SELECT * FROM equipes");
		if (!$equipeQuery){
			return false;
		}
		$equipeQuery->setFetchMode(PDO::FETCH_OBJ);
		while($equipeFetch = $equipeQuery->fetch()){
			array_push($equipes, new equipe($equipeFetch->idEquipe, $equipeFetch->nomEquipe));
		}

		return $equipes;
	}

	/**
	* Obtenir toutes les équipes d'un site
	*/
	public static function getAllFromSite($site){
		$equipes = array();

		$bdd = new BDD();

		$equipeQuery = $bdd->_connexion->Query("SELECT * FROM equipes WHERE idSite = " . $site->_id);
		if (!$equipeQuery){
			return false;
		}
		$equipeQuery->setFetchMode(PDO::FETCH_OBJ);
		while($equipeFetch = $equipeQuery->fetch()){
			array_push($equipes, new equipe($equipeFetch->idEquipe, $equipeFetch->nomEquipe));
		}

		return $equipes;
	}

	public function delete(){
		$bdd = new BDD();

		$bdd->_connexion->Query("UPDATE stagiaire SET equipe = NULL WHERE equipe = ".$this->_id);
		$bdd->_connexion->Query("DELETE FROM equipes WHERE idEquipe = ".$this->_id." AND idSite = " . $this->_site->_id);
	}

	public function create(){
		$bdd = new BDD();

		$nomQuote = $bdd->_connexion->Quote($this->_nom);
		$idSiteQuote = $bdd->_connexion->Quote($this->_site->_id);

		$bdd->_connexion->Query("INSERT INTO equipes (nomEquipe, idSite) VALUES({$nomQuote}, {$idSiteQuote})");
	}
}
?>