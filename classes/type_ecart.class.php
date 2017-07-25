<?php
include_once 'bdd.class.php';
include_once 'site.class.php';
include_once 'user.class.php';
include_once 'missions.class.php';
include_once 'statut.class.php';


/**
* Permet de gérer les éléments relatifs aux écarts
*/
class TypeEcart
{
	public $_id;
	public $_nom;
	public $_site;
	
	function __construct($id){
		$this->_id = $id;
		$this->getInfos();
	}

	/**
	* Obtenir les informations concernant un écart
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$siteQuery = $bdd->_connexion->Query("SELECT * FROM type_ecart WHERE idType = " . $id);
		if (!$siteQuery){
			return false;
		}
		$siteQuery->setFetchMode(PDO::FETCH_OBJ);
		$siteFetch = $siteQuery->fetch();

		$this->_nom = $siteFetch->NomType;
		$this->_site = new Site($siteFetch->Site);
		
		return true;
	}

		public static function getNewTypeEcartNumber(){
		$bdd = new BDD();

		/* Récupération de tous les tuples stages ayant la session courante par ordre descendant*/
		$temp=$bdd->_connexion->Query('SELECT * FROM type_ecart ORDER BY idType DESC');
		/*Récupération du premier stage de la liste*/
		$typeActuel=$temp->fetch();
		/*Incrémentation pour avoir l'ID du stage en cours de création*/
		$idTypeActuel=$typeActuel['idType']+1;

		return $idTypeActuel;
	}
	
	/**
	* Obtenir toutes les FAP selon le site
	*/
	public static function getAllFromSite($site){
		$listTypeEcart = array();

		$bdd = new BDD();

		$stagiaireQuery =$bdd->_connexion->query("SELECT idType FROM type_ecart WHERE Site={$site}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listTypeEcart, new TypeEcart($stagiaireFetch->idType));
		}

		return $listTypeEcart;
	}
	
	/**
	* Créer un nouveau type d'écart
	*/
	public static function creer($id, $titre, $site){
		$bdd = new BDD();

		$site = $bdd->_connexion->Quote($site->_id);
		$id = $bdd->_connexion->Quote($id);
		$titre = $bdd->_connexion->Quote($titre);
		

		$bdd->_connexion->Query("INSERT INTO type_ecart(idType, NomType, Site) VALUES({$id}, {$titre}, {$site})");
	}

}
?>
