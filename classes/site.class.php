<?php
include_once 'bdd.class.php';

/**
* Classe représentant un site de formation
*/
class Site
{
	public $_id;
	public $_nom;
	

	function __construct($id, $nom = null){
		$this->_id = $id;
		$this->_nom = $nom;
		if (is_null($nom)){
			$this->getInfos();
		}
	}

	/**
	* Obtenir les informations concernant un site
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$siteQuery = $bdd->_connexion->Query("SELECT * FROM site WHERE idSite = " . $id);
		if (!$siteQuery){
			return false;
		}
		$siteQuery->setFetchMode(PDO::FETCH_OBJ);
		$siteFetch = $siteQuery->fetch();

		
		$this->_nom      = $siteFetch->NomSite;

		return true;
	}

	/**
	* Ajouter un site en base de données
	*/
	public static function create($nom){
		$bdd = new BDD();

		$nom = $bdd->_connexion->Quote($nom);

		$siteQuery = $bdd->_connexion->Query("INSERT INTO site (nomSite) VALUES({$nom})");
	}

	/**
	* Permet d'obtenir la liste des sites
	*/
	public static function getAll(){
		$sites = array();

		$bdd = new BDD();
		$siteQuery = $bdd->_connexion->Query("SELECT * FROM site");
		if (!$siteQuery){
			return false;
		}
		$siteQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($siteFetch = $siteQuery->fetch()){
			array_push($sites, new Site($siteFetch->idSite));
		}

		return $sites;
	}

	/**
	* Permet de vérifier que le mot de passe en base est conforme a celui donné lors de la connexion
	*/
	public function verifPass($pass){
	//	if ($this->_password == hash('ripemd160', $pass)){
			return true;
	//	}

	//	return false;
	}

	/**
	* Supprimer un site en base de données
	*/
	public function delete(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$siteQuery = $bdd->_connexion->Query("DELETE FROM site WHERE idSite = {$id}");
	}
}
?>