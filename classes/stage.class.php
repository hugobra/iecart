<?php
include_once 'bdd.class.php';
include_once 'site.class.php';
include_once 'theme.class.php';
include_once 'objectif.class.php';
include_once 'fonction.class.php';
include_once 'OPS.class.php';

/**
* Classe de stage
*/
class Stage
{
	public $_id;
	public $_site;
	public $_date;
	public $_titre;
	public $_duree;
	public $_codeAction;
	public $_valide;


	function __construct($id, $site = null, $date = null, $titre = null, $duree = null, $valide = null, $codeAction = null){
		$this->_id         = $id;
		$this->_site       = $site;
		$this->_date       = $date;
		$this->_titre      = $titre;
		$this->_duree      = $duree;
		$this->_valide     = $valide;
		$this->_codeAction = $codeAction;

		if (empty($_site) && empty($_date) && empty($_titre) && empty($_duree) && empty($_valide)){
			$this->getInfos();
		}
	}

	/**
	* Obtenir les informations concernant un stage
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$stageQuery = $bdd->_connexion->Query("SELECT * FROM stage WHERE idstage = " . $id);
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		$stageFetch = $stageQuery->fetch();

		$this->_site       = new Site($stageFetch->idSite);
		$this->_date       = $stageFetch->date;
		$this->_titre      = $stageFetch->titre;
		$this->_duree      = $stageFetch->duree;
		$this->_codeAction = $stageFetch->codeAction;
		$this->_valide     = $stageFetch->valide;

		return true;
	}

	/**
	* Permet d'obtenir l'id du prochain stage à ajouter, se charge d'incrémenter la variable de stage
	* Réutilisation du code précédents devs
	*/
	public static function getNewStageNumber(){
		$bdd = new BDD();

		/* Récupération de tous les tuples stages ayant la session courante par ordre descendant*/
		$temp=$bdd->_connexion->Query('SELECT * FROM stage ORDER BY idStage DESC');
		/*Récupération du premier stage de la liste*/
		$stageActuel=$temp->fetch();
		/*Incrémentation pour avoir l'ID du stage en cours de création*/
		$idStageActuel=$stageActuel['idStage']+1;

		return $idStageActuel;
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
	public static function creer($site, $date, $titre, $duree, $codeAction){
		$bdd = new BDD();

		$site = $bdd->_connexion->Quote($site->_id);
		$date = $bdd->_connexion->Quote($date);
		$titre = $bdd->_connexion->Quote($titre);
		$duree = $bdd->_connexion->Quote($duree);
		$codeAction = $bdd->_connexion->Quote($codeAction);

		$bdd->_connexion->Query("INSERT INTO stage(idSite, date, titre, duree, codeAction) VALUES({$site}, {$date}, {$titre}, {$duree}, {$codeAction})");
	}

	/**
	* Obtenir la liste des stages en fonction du site
	*/
	public static function getAllFromSite($site){
		$stages = array();

		$bdd = new BDD();

		$siteId = $bdd->_connexion->Quote($site->_id);

		$stageQuery = $bdd->_connexion->Query("SELECT * FROM stage WHERE idSite = {$siteId} AND valide = 1 ORDER BY titre");
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stageFetch = $stageQuery->fetch()){
			array_push($stages, new Stage($stageFetch->idStage, $site, $stageFetch->date, $stageFetch->titre, $stageFetch->duree, $stageFetch->valide));
		}

		return $stages;
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
	public function modifier($nom, $duree, $codeAction){
		$bdd = new BDD();

		$nom   = $bdd->_connexion->Quote($nom);
		$duree = $bdd->_connexion->Quote($duree);
		$codeAction = $bdd->_connexion->Quote($codeAction);

		$bdd->_connexion->query("UPDATE stage SET titre = {$nom}, duree = {$duree}, codeAction = {$codeAction} WHERE idStage = {$this->_id}");
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
