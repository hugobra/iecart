<?php
include_once 'bdd.class.php';
include_once 'site.class.php';
include_once '../classes/type_ecart.class.php';



/**
* Classe de stage
*/
class Mission
{
	public $_id;
	public $_titre;
	public $_responsable;
	public $_description;
	public $_site;
	public $_typeécart;
	
	
	


	function __construct($id, $site = null, $titre = null, $responsable = null, $description = null, $typeécart=null){
		$this->_id         = $id;
		$this->_site       = $site;
		$this->_responsable      = $responsable;
		$this->_description      = $description;
		$this->_titre      = $titre;
		$this->_typeécart    = $typeécart;

		if (empty($_site) && empty($_description) && empty($_titre) && empty($_responsable) ){
			$this->getInfos();
		}
	}

	/**
	* Obtenir les informations concernant un stage
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$stageQuery = $bdd->_connexion->Query("SELECT * FROM missions WHERE idMission = " . $id);
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		$stageFetch = $stageQuery->fetch();

		$this->_site       = new Site($stageFetch->Site);
		$this->_responsable       = new UserManager($stageFetch->Responsable);
		$this->_titre      = $stageFetch->Titre;
		$this->_description     = $stageFetch->Description;
		$this->_typeécart     = new TypeEcart($stageFetch->TypeEcart);
		

		return true;
	}

	/**
	* Permet d'obtenir l'id du prochain stage à ajouter, se charge d'incrémenter la variable de stage
	* Réutilisation du code précédents devs
	*/
	public static function getNewMissionNumber(){
		$bdd = new BDD();

		/* Récupération de tous les tuples stages ayant la session courante par ordre descendant*/
		$temp=$bdd->_connexion->Query('SELECT * FROM missions ORDER BY idMission DESC');
		/*Récupération du premier stage de la liste*/
		$stageActuel=$temp->fetch();
		/*Incrémentation pour avoir l'ID du stage en cours de création*/
		$idMissionActuel=$stageActuel['idMission']+1;

		return $idMissionActuel;
	}

	public static function getLastStageForSite($site){
		$bdd = new BDD();

		$temp = $bdd->_connexion->Query("SELECT * FROM stage WHERE idSite = ".$site->_id." ORDER BY idStage DESC LIMIT 0,1");
		$stageActuel=$temp->fetch();

		return new Stage($stageActuel['idStage']);
	}

	/**
	* Créer un nouveau stage
	*/
	public static function creer($id, $titre, $description, $responsable, $site,$typeécart){
		$bdd = new BDD();

		$site = $bdd->_connexion->Quote($site->_id);
		$id = $bdd->_connexion->Quote($id);
		$titre = $bdd->_connexion->Quote($titre);
		$description = $bdd->_connexion->Quote($description);
		$responsable = $bdd->_connexion->Quote($responsable);
		$typeécart = $bdd->_connexion->Quote($typeécart);

		$bdd->_connexion->Query("INSERT INTO missions(idMission, Titre, Description, Responsable, Site,TypeEcart) VALUES({$id}, {$titre}, {$description}, {$responsable}, {$site},{$typeécart})");
	}

	/**
	* Obtenir la liste des stages en fonction du site
	*/
	public static function getAllFromSite($site){
		$missions = array();

		$bdd = new BDD();

		$siteId = $bdd->_connexion->Quote($site->_id);

		$stageQuery = $bdd->_connexion->Query("SELECT * FROM missions WHERE Site = {$siteId} ");
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stageFetch = $stageQuery->fetch()){
			array_push($missions, new Mission($stageFetch->idMission));
		}

		return $missions;
	}

	/**
	* Obtenir tous les thème pour le stage
	*/
	public function getThemes(){
		$themes = array();

		$bdd = new BDD();

		$themesQuery = $bdd->_connexion->query('SELECT * FROM themes WHERE idStage='.$this->_id);
		if (!$themesQuery){
			return false;
		}
		$themesQuery->setFetchMode(PDO::FETCH_OBJ);
		while($themeFetch = $themesQuery->fetch()){
			array_push($themes, new Theme($themeFetch->idTheme, $themeFetch->idStage, $themeFetch->theme));
		}

		return $themes;
	}

	/**
	* Obtenir tous les objectifs pour le stage
	*/
	public function getObjectifs(){
		$objectifs = array();

		$bdd = new BDD();

		$stage = $bdd->_connexion->Quote($this->_id);

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifsstage WHERE idStage = {$stage}");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_push($objectifs, new Objectif($objectifFetch->idObjectif, $stage, new Fonction($objectifFetch->idFonction), $objectifFetch->nomObjectif));
		}

		return $objectifs;
	}

	/**
	* Ajouter un objectif pour une fonction et pour ce stage
	*/
	public function addObjectif($objectif, $fonction){
		$bdd = new BDD();

		$objectif = $bdd->_connexion->Quote($objectif);
		$fonction = $bdd->_connexion->Quote($fonction);

		$bdd->_connexion->Query("INSERT INTO objectifsstage (idstage, idFonction, nomObjectif) VALUES({$this->_id}, {$fonction}, {$objectif})");
	}

	/**
	* Ajouter un thème au stage
	*/
	public function addTheme($theme){
		$bdd = new BDD();

		$theme = $bdd->_connexion->Quote($theme);

		$bdd->_connexion->Query("INSERT INTO themes (idstage, theme) VALUES({$this->_id}, {$theme})");
	}

	/**
	* Supprimer un thème du stage
	*/
	public function supprimerTheme($id){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($id);

		$bdd->_connexion->query("DELETE FROM themes WHERE idTheme = {$id}");
	}

	/**
	* Supprimer un objecif du stage
	*/
	public function supprimerObjectif($id){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($id);

		$bdd->_connexion->query("DELETE FROM objectifsstage WHERE idObjectif = {$id}");
	}

	/**
	* Modifier le stage en base de donnée (Nom et duree uniquement)
	*/
	public function modifier($titre, $description, $responsable,$typeécart){
		$bdd = new BDD();

		$titre   = $bdd->_connexion->Quote($titre);
		$description = $bdd->_connexion->Quote($description);
		$responsable = $bdd->_connexion->Quote($responsable);
		$$typeécart = $bdd->_connexion->Quote($$typeécart);

		$bdd->_connexion->query("UPDATE missions SET Titre = {$titre}, Description = {$description}, Responsable = {$responsable}, TypeEcart={$typeécart} WHERE idMission = {$this->_id}");
	}

	/**
	* Supprimer purement et simplement le stage
	*/
	public function supprimer(){
		$bdd = new BDD();

		/*$bdd->_connexion->query("DELETE FROM objectifsstage WHERE idStage = " . $this->_id);
		$bdd->_connexion->query("DELETE FROM stage WHERE idStage = " . $this->_id);*/

		$bdd->_connexion->query("UPDATE stage SET valide = 0 WHERE idStage = " . $this->_id);
	}


/* récupérer les OPS relatif à l'objectif, la fonction et le stage */
	public static function getAllFromStageObjectifAndFonction($stage, $objectif, $fonction){
		$opss = array();

		$bdd = new BDD();

		$stage = $bdd->_connexion->Quote($stage->_id);
		//$objectif = $bdd->_connexion->Quote($objectif->_id);
		//$fonction = $bdd->_connexion->Quote($fonction->_id);

		$opsQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idStage = {$stage} AND idObjectif={$objectif} AND idFonction = {$fonction} ORDER BY idOPS DESC");
		if (!$opsQuery){
			return false;
		}
		$opsQuery->setFetchMode(PDO::FETCH_OBJ);
		while($opsFetch = $opsQuery->fetch()){
			array_unshift($opss, new OPS($opsFetch->idOPS, $stage, $objectif, $fonction, $opsFetch->nomOPS));
		}

		return $opss;
	} 
	
	public static function getAllFromStageObjectifAndFonctionpourcréerstage($stage, $objectif, $fonction){
		$opss = array();

		$bdd = new BDD();


		$opsQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idStage = {$stage} AND idObjectif={$objectif} AND idFonction = {$fonction} ORDER BY idOPS DESC");
		if (!$opsQuery){
			return false;
		}
		$opsQuery->setFetchMode(PDO::FETCH_OBJ);
		while($opsFetch = $opsQuery->fetch()){
			array_unshift($opss, new OPS($opsFetch->idOPS, $stage, $objectif, $fonction, $opsFetch->nomOPS));
		}

		return $opss;
	} 
	
	public static function getOPSfromstagepourcréerFAP($stage){
		$opss = array();

		$bdd = new BDD();

		$stage = $bdd->_connexion->Quote($stage);
	

		$objectifQuery = $bdd->_connexion->Query("SELECT * FROM ops WHERE idStage = {$stage}  ");
		if (!$objectifQuery){
			return false;
		}
		$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
		while($objectifFetch = $objectifQuery->fetch()){
			array_unshift($opss, new Objectif($objectifFetch->idOPS, $stage, $objectifFetch->idObjectif, $objectifFetch->idFonction, $objectifFetch->nomOPS));
		}

		return $opss;
	}
		
	public static function addOPS($stage,$idobjectif, $fonction,$ops){
		$bdd = new BDD();

		$idobjectif = $bdd->_connexion->Quote($idobjectif);
		$fonction = $bdd->_connexion->Quote($fonction);
		$ops=$bdd->_connexion->Quote($ops);
		$stage=$bdd->_connexion->Quote($stage);

		$bdd->_connexion->Query("INSERT INTO ops (idStage, idObjectif, idFonction, nomOPS) VALUES({$stage},{$idobjectif}, {$fonction}, {$ops})");
	}
	
	public function supprimerOPS($id){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($id);

		$bdd->_connexion->Query("DELETE FROM ops WHERE idOPS = {$id}");
	}
	
	public static function getAllStagesFromSiteAndFonction($site,$fonction){
		$stages = array();

		$bdd = new BDD();

		$siteId = $bdd->_connexion->Quote($site->_id);
		$fonction = $bdd->_connexion->Quote($fonction);

		$stageQuery = $bdd->_connexion->Query("SELECT DISTINCT stage.idStage,stage.date,stage.titre,stage.duree,stage.valide FROM objectifsstage INNER JOIN stage ON objectifsstage.idStage=stage.idStage WHERE idFonction={$fonction} AND idSite = {$siteId} AND valide = 1 ORDER BY titre");
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stageFetch = $stageQuery->fetch()){
			array_push($stages, new Stage($stageFetch->idStage, $site, $stageFetch->date, $stageFetch->titre, $stageFetch->duree, $stageFetch->valide));
		}

		return $stages;
	}
}
?>
