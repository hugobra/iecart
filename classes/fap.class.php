<?php
include_once 'bdd.class.php';
include_once 'stagiaire.class.php';
include_once 'site.class.php';
include_once 'stage.class.php';
include_once 'fonction.class.php';
include_once 'objectif.class.php';

/**
* Permet de gérer les éléments relatifs aux FAP
*/
class FAP 
{
	public $_id;
	public $_stage;
	public $_stagiaire;
	public $_idSite;
	public $_dateModif;
	public $_codeAction;
	public $_codeSession;
	public $_dateCreation;
	public $_formateur;
	public $_fonction;
	public $_dateDebut;
	public $_dateFin;
	public $_objectifs;
	public $_commentaires;
	public $_commentairesHierar;
	public $_nbabsence;
	public $_absenceD;
	public $_absenceF;
	public $_statut;
	
	function __construct($id){
		$this->_id = $id;
		$this->getInfos();
	}

	/**
	* Obtenir les informations concernant une fap
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$siteQuery = $bdd->_connexion->Query("SELECT * FROM fap WHERE idFap = " . $id);
		if (!$siteQuery){
			return false;
		}
		$siteQuery->setFetchMode(PDO::FETCH_OBJ);
		$siteFetch = $siteQuery->fetch();

		$this->_stage = new Stage($siteFetch->idStage);
		$this->_stagiaire = new Stagiaire($siteFetch->idStagiaire);
		$this->_idSite = $siteFetch->idSite;
		$this->_dateModif = $siteFetch->dateModif;
		$this->_codeAction = $siteFetch->codeAction;
		$this->_codeSession = $siteFetch->codeSession;
		$this->_dateCreation = $siteFetch->dateCreation;
		$this->_formateur = $siteFetch->Formateur;
		$this->_fonction = new Fonction($siteFetch->fonction);
		$this->_dateDebut = $siteFetch->dateDebut;
		$this->_dateFin = $siteFetch->dateFin;
		$this->_commentaires = $siteFetch->commentaires;
		$this->_commentairesHierar = $siteFetch->commentairesHierar;
		$this->_nbabsence = $siteFetch->nbabsence;
		$this->_absenceD = $siteFetch->absenceD;
		$this->_absenceF = $siteFetch->absenceF;
		$this->_statut = $siteFetch->statut;

		$objectifs = array();
		$objQuery = $bdd->_connexion->Query("SELECT * FROM objectifs WHERE idFap = " . $id);
		if (!$objQuery){
			return false;
		}
		$objQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($objFetch = $objQuery->fetch()){
			array_unshift($objectifs, new Objectif($objFetch->objectif, null, null, null, true, $this->_id));
		}

		$this->_objectifs = $objectifs;

		return true;
	}

	/**
	* Obtenir toutes les FAP selon le site
	*/
	public static function getAllFromSite($site){
		$listFap = array();

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query('SELECT idFAP FROM fap WHERE idSite='.$site->_id);
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listFap, new FAP($stagiaireFetch->idFAP));
		}

		return $listFap;
	}

	/**
	* Methode plus rapide pour lister les FAPS, sans instanciation d'objet
	*/
	public static function getAllFromSiteWithoutObject($site){
		$listFap = array();

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT fap.idFAP, stage.titre, stagiaire.nom, stagiaire.prenom, stagiaire.idStagiaire, fap.dateModif, fap.Formateur , fap.statut FROM stage, fap, stagiaire WHERE fap.idSite={$site->_id} AND fap.idStage=stage.idStage AND fap.idStagiaire=stagiaire.idStagiaire");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listFap, $stagiaireFetch);
		}

		return $listFap;
	}
	
	/**
	* Methode plus rapide pour lister les FAPS, sans instanciation d'objet
	*/
	public static function getAllFromSiteAppuiFormation($site){
		$listFap = array();

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT fap.idFAP, stage.titre, stagiaire.nom, stagiaire.prenom, stagiaire.idStagiaire, fap.dateModif, fap.Formateur , fap.statut FROM stage, fap, stagiaire WHERE fap.idSite={$site->_id} AND fap.idStage=stage.idStage AND fap.idStagiaire=stagiaire.idStagiaire AND fap.statut=1");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listFap, $stagiaireFetch);
		}

		return $listFap;
	}
	
	/**
	* Methode plus rapide pour lister les FAPS, pour un MPL
	*/
	public static function getAllFromSiteMPL($site,$service,$equipe,$tranche){
		$listFap = array();

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT fap.idFAP, stage.titre, stagiaire.nom, stagiaire.prenom, stagiaire.idStagiaire, fap.dateModif, fap.Formateur , fap.statut FROM stage, fap, stagiaire WHERE fap.idSite={$site->_id} AND fap.idStage=stage.idStage AND fap.idStagiaire=stagiaire.idStagiaire AND stagiaire.service = {$service} AND stagiaire.equipe={$equipe} AND stagiaire.tranche={$tranche} AND fap.statut=1");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listFap, $stagiaireFetch);
		}

		return $listFap;
	}

	/**
	* Methode plus rapide pour lister les FAPS, pour un CDS
	*/
	public static function getAllFromSiteCDS($site,$service){
		$listFap = array();

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT fap.idFAP, stage.titre, stagiaire.nom, stagiaire.prenom, stagiaire.idStagiaire, fap.dateModif, fap.Formateur , fap.statut FROM stage, fap, stagiaire WHERE fap.idSite={$site->_id} AND fap.idStage=stage.idStage AND fap.idStagiaire=stagiaire.idStagiaire AND stagiaire.service = {$service} AND fap.statut=1 ");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listFap, $stagiaireFetch);
		}

		return $listFap;
	}
	
	/**
	* Ajouter la FAP à la base de données
	*/
	public static function create($id, $stage, $stagiaire, $site, $codeAction, $codeSession, $dateCreation, $formateur, $fonction, $dateDebut, $dateFin, $nbabsence, $absenceD, $absenceF, $statut, $objectifs){
		$bdd = new BDD();
		
		$id = $bdd->_connexion->Quote($id);
		$stage = $bdd->_connexion->Quote($stage->_id);
		$stagiaire = $bdd->_connexion->Quote($stagiaire->_id);
		$site = $bdd->_connexion->Quote($site->_id);
		$codeAction = $bdd->_connexion->Quote($codeAction);
		$codeSession = $bdd->_connexion->Quote($codeSession);
		$dateCreation = $bdd->_connexion->Quote($dateCreation);
		$formateur = $bdd->_connexion->Quote($formateur);
		$fonction = $bdd->_connexion->Quote($fonction);
		$dateDebut = $bdd->_connexion->Quote($dateDebut);
		$dateFin = $bdd->_connexion->Quote($dateFin);
		$nbabsence = $bdd->_connexion->Quote($nbabsence);
		$absenceD = $bdd->_connexion->Quote($absenceD);
		$absenceF= $bdd->_connexion->Quote($absenceF);
		$statut= $bdd->_connexion->Quote($statut);

		//echo("INSERT INTO fap(idFAP, idStage, idStagiaire, idSite, codeAction, codeSession, dateCreation, Formateur, fonction, dateDebut, dateFin) VALUES({$id}, {$stage}, {$stagiaire}, {$site}, {$codeAction}, {$codeSession}, {$dateCreation}, {$formateur}, {$fonction}, {$dateDebut}, {$dateFin})");

		$reqFap=$bdd->_connexion->Query("INSERT INTO fap(idFAP, idStage, idStagiaire, idSite, codeAction, codeSession, dateCreation, Formateur, fonction, dateDebut, dateFin, nbabsence, absenceD, absenceF, statut) VALUES({$id}, {$stage}, {$stagiaire}, {$site}, {$codeAction}, {$codeSession}, {$dateCreation}, {$formateur}, {$fonction}, {$dateDebut}, {$dateFin}, {$nbabsence}, {$absenceD}, {$absenceF},{$statut})");
	
		foreach ($objectifs as $objectif) {
			//var_dump($objectif);
			$objectifId = $bdd->_connexion->Quote($objectif["objectifId"]);
			$PF = $bdd->_connexion->Quote($objectif["PF"]);
			$PA = $bdd->_connexion->Quote($objectif["PA"]);

			$bdd->_connexion->Query("INSERT INTO objectifs(idFap, objectif, pointFort, pointAAmeliorer) VALUES ({$id}, {$objectifId}, {$PF}, {$PA})");
		}
	}

	/**
	* Supprimer une FAP de la base de données
	*/
	public function removefromDatabase(){
		$bdd = new BDD();

		$bdd->_connexion->query('DELETE FROM fap WHERE idFAP='.$this->_id);
	}

	/**
	* Obtenir de dernier id de FAP utilisé
	*/
	public static function getNewFapNumber(){
		$bdd = new BDD();

		$faps=$bdd->_connexion->query('SELECT * FROM fap ORDER BY idFAP DESC');
		$fap=$faps->fetch();
		$idFAP=$fap['idFAP']+1;

		return $idFAP;
	}

	/**
	* Modifier une FAP existante
	*/
	public function modifier($codeAction, $codeSession, $idStage, $formateur, $dateDebut, $dateFin, $nbabsence, $absenceD, $absenceF, $statut, $objectifs){
		$bdd = new BDD();

		$codeAction = $bdd->_connexion->Quote($codeAction);
		$codeSession = $bdd->_connexion->Quote($codeSession);
		$idStage = $bdd->_connexion->Quote($idStage);
		$formateur = $bdd->_connexion->Quote($formateur);
		$dateDebut = $bdd->_connexion->Quote($dateDebut);
		$dateFin = $bdd->_connexion->Quote($dateFin);
		$nbabsence= $bdd->_connexion->Quote($nbabsence);
		$absenceD= $bdd->_connexion->Quote($absenceD);
		$absenceF= $bdd->_connexion->Quote($absenceF);
		$statut= $bdd->_connexion->Quote($statut);

		$reqFAP = $bdd->_connexion->Query("UPDATE fap SET codeAction = {$codeAction}, codeSession = {$codeSession}, idStage= {$idStage}, formateur= {$formateur}, dateDebut= {$dateDebut}, dateFin= {$dateFin}, nbabsence={$nbabsence},absenceD={$absenceD}, absenceF={$absenceF}, statut={$statut}  WHERE idFAP = {$this->_id}");
	
		$bdd->_connexion->Query("DELETE FROM objectifs WHERE idFap = {$this->_id}");
	
		foreach ($objectifs as $objectif) {
			//var_dump($objectif);
			$objectifId = $bdd->_connexion->Quote($objectif["objectifId"]);
			$PF = $bdd->_connexion->Quote($objectif["PF"]);
			$PA = $bdd->_connexion->Quote($objectif["PA"]);

			// $bdd->_connexion->Query("DELETE FROM objectifs WHERE idFap = {$this->_id} AND objectif = {$objectifId}");
			$bdd->_connexion->Query("INSERT INTO objectifs(idFap, objectif, pointFort, pointAAmeliorer) VALUES ({$this->_id}, {$objectifId}, {$PF}, {$PA})");
		}
	}

	/**
	* Ajouter un commentaire de stagiaire sur une FAP
	*/
	public function commenter($commentaire){
		$bdd = new BDD();

		$commentaire = $bdd->_connexion->Quote($commentaire);

		$bdd->_connexion->Query("UPDATE fap SET commentaires = {$commentaire} WHERE idFAP = {$this->_id}");
	}

	/**
	* ajouter un commentaire de la hiérarchie sur une FAP
	*/
	public function commenterHierar($commentaire){
		$bdd = new BDD();

		$commentaire = $bdd->_connexion->Quote($commentaire);

		$bdd->_connexion->Query("UPDATE fap SET commentairesHierar = {$commentaire} WHERE idFAP = {$this->_id}");
	}

	public function changerstatut($statut){
		$bdd = new BDD();
		
		//$statut = $bdd->_connexion->Quote($statut);

		$bdd->_connexion->Query("UPDATE fap SET statut = {$statut} WHERE idFAP = {$this->_id}");
	}
	
	public static function getAllBetweenDatesFromSite2($dateDebut, $dateFin, $site){
		$listFaps = array();

		$dateDebutExplode = explode("/", $dateDebut);
		$dateDebutFomat = intval($dateDebutExplode[2] . $dateDebutExplode[1] . $dateDebutExplode[0]);

		$dateFinExplode = explode("/", $dateFin);
		$dateFinFomat = intval($dateFinExplode[2] . $dateFinExplode[1] . $dateFinExplode[0]);

		$bdd = new BDD();

		// $stagiaireQuery = $faps=$bdd->_connexion->query('SELECT * FROM fap WHERE idSite ='.$site->_id.' ORDER BY idStage');
		// $stagiaireQuery = $faps=$bdd->_connexion->query('SELECT * FROM fap f JOIN objectifs o ON (o.idFap = f.idFAP) JOIN objectifsstage os ON (os.idObjectif = o.idObjectif) WHERE idSite ='.$site->_id.' ORDER BY f.idStage, idFonction');
		$stagiaireQuery = $faps=$bdd->_connexion->query('SELECT * FROM objectifsstage os JOIN objectifs o ON (o.objectif = os.idObjectif) JOIN fap f ON(f.idFAP = o.idFap) WHERE f.idSite ='.$site->_id.' ORDER BY f.idStage, idFonction, os.nomObjectif');
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			$dateDebutfapExplode = explode("/", $stagiaireFetch->dateDebut);
			$dateDebutfapFomat = intval($dateDebutfapExplode[2] . $dateDebutfapExplode[1] . $dateDebutfapExplode[0]);

			$dateFinfapExplode = explode("/", $stagiaireFetch->dateFin);
			$dateFinfapFomat = intval($dateFinfapExplode[2] . $dateFinfapExplode[1] . $dateFinfapExplode[0]);

			if ($dateDebutfapFomat >= $dateDebutFomat && $dateFinfapFomat <= $dateFinFomat){
				array_push($listFaps, $stagiaireFetch);
			}
		}

		return $listFaps;
	}

	public static function getAllBetweenDatesFromSite($dateDebut, $dateFin, $site){
		$listFaps = array();

		$dateDebutExplode = explode("/", $dateDebut);
		$dateDebutFomat = intval($dateDebutExplode[2] . $dateDebutExplode[1] . $dateDebutExplode[0]);

		$dateFinExplode = explode("/", $dateFin);
		$dateFinFomat = intval($dateFinExplode[2] . $dateFinExplode[1] . $dateFinExplode[0]);

		$bdd = new BDD();

		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			$dateDebutfapExplode = explode("/", $stagiaireFetch->dateDebut);
			$dateDebutfapFomat = intval($dateDebutfapExplode[2] . $dateDebutfapExplode[1] . $dateDebutfapExplode[0]);

			$dateFinfapExplode = explode("/", $stagiaireFetch->dateFin);
			$dateFinfapFomat = intval($dateFinfapExplode[2] . $dateFinfapExplode[1] . $dateFinfapExplode[0]);

			if ($dateDebutfapFomat >= $dateDebutFomat && $dateFinfapFomat <= $dateFinFomat){
				array_push($listFaps, $stagiaireFetch);
			}
		}

		return $listFaps;
	}

	public static function getAllBetweenDatesFromSiteWithFilters($dateDebut, $dateFin, $site, $idStage, $idFonction,$service,$equipe,$tranche){
		$listFaps = array();

		$dateDebutExplode = explode("/", $dateDebut);
		$dateDebutFomat = intval($dateDebutExplode[2] . $dateDebutExplode[1] . $dateDebutExplode[0]);

		$dateFinExplode = explode("/", $dateFin);
		$dateFinFomat = intval($dateFinExplode[2] . $dateFinExplode[1] . $dateFinExplode[0]);

		$bdd = new BDD();

		/*if ($idStage == 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire)   WHERE f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche}  ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idStage = {$idStage} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage == 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire) WHERE f.idStage = {$idStage} AND os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche} ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else{
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}*/
		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT s.titre,o.pointFort,o.pointAAmeliorer,os.nomObjectif,f.idStagiaire,f.idFAP,f.dateDebut,f.dateFin,f.codeAction FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire) WHERE f.idStage = {$idStage} AND os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche} ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			$dateDebutfapExplode = explode("/", $stagiaireFetch->dateDebut);
			$dateDebutfapFomat = intval($dateDebutfapExplode[2] . $dateDebutfapExplode[1] . $dateDebutfapExplode[0]);

			$dateFinfapExplode = explode("/", $stagiaireFetch->dateFin);
			$dateFinfapFomat = intval($dateFinfapExplode[2] . $dateFinfapExplode[1] . $dateFinfapExplode[0]);

			if ($dateDebutfapFomat >= $dateDebutFomat && $dateFinfapFomat <= $dateFinFomat){
				array_push($listFaps, $stagiaireFetch);
			}
		}

		return $listFaps;
	}
	
	public static function getAllBetweenDatesFromSiteWithFiltersmulti($listFaps, $dateDebut, $dateFin, $site, $idStage, $idFonction,$service,$equipe,$tranche){
		
		
		$dateDebutExplode = explode("/", $dateDebut);
		$dateDebutFomat = intval($dateDebutExplode[2] . $dateDebutExplode[1] . $dateDebutExplode[0]);

		$dateFinExplode = explode("/", $dateFin);
		$dateFinFomat = intval($dateFinExplode[2] . $dateFinExplode[1] . $dateFinExplode[0]);

		$bdd = new BDD();

		/*if ($idStage == 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idSite = ".$site->_id."  ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idStage = {$idStage} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage == 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idStage = {$idStage} AND os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else{
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}*/
		$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT s.titre,o.pointFort,o.pointAAmeliorer,os.nomObjectif,f.idStagiaire,f.idFAP,f.dateDebut,f.dateFin,f.codeAction FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire) WHERE f.idStage = {$idStage} AND os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche} ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			$dateDebutfapExplode = explode("/", $stagiaireFetch->dateDebut);
			$dateDebutfapFomat = intval($dateDebutfapExplode[2] . $dateDebutfapExplode[1] . $dateDebutfapExplode[0]);

			$dateFinfapExplode = explode("/", $stagiaireFetch->dateFin);
			$dateFinfapFomat = intval($dateFinfapExplode[2] . $dateFinfapExplode[1] . $dateFinfapExplode[0]);

			if ($dateDebutfapFomat >= $dateDebutFomat && $dateFinfapFomat <= $dateFinFomat){
				array_push($listFaps, $stagiaireFetch);
			}
		}

		return $listFaps;
	}
	
	public static function getAllBetweenDatesFromSiteWithFiltersaveclistes($dateDebut, $dateFin, $site, $idStages, $idFonctions,$services,$equipes,$tranches){
		$listFaps = array();

		$dateDebutExplode = explode("/", $dateDebut);
		$dateDebutFomat = intval($dateDebutExplode[2] . $dateDebutExplode[1] . $dateDebutExplode[0]);

		$dateFinExplode = explode("/", $dateFin);
		$dateFinFomat = intval($dateFinExplode[2] . $dateFinExplode[1] . $dateFinExplode[0]);

		$bdd = new BDD();
		//$stages=array_map('intval', explode(',', $idStages));
		$stages = implode(",",$idStages);
		//$fonctions=array_map('intval', explode(',', $idFonctions));
		$fonctions = implode(",",$idFonctions);
		//$services=array_map('intval', explode(',', $services));
		$services2 = implode(",",$services);
		//$equipes=array_map('intval', explode(',', $equipes));
		$equipes2 = implode(",",$equipes);
		//$tranches=array_map('intval', explode(',', $tranches));
		$tranches2 = implode(",",$tranches);
		
						
		/*if ($idStage == 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire)   WHERE f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche}  ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction == 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idStage = {$idStage} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage == 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else if($idStage != 0 && $idFonction != 0){
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire) WHERE f.idStage = {$idStage} AND os.idFonction = {$idFonction} AND f.idSite = ".$site->_id." AND st.service={$service} AND st.equipe={$equipe} AND st.tranche={$tranche} ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}else{
			$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT * FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) WHERE f.idSite = ".$site->_id." ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		}*/
	$stagiaireQuery = $faps=$bdd->_connexion->query("SELECT s.titre,o.pointFort,o.pointAAmeliorer,os.nomObjectif,f.idStagiaire,f.idFAP,f.dateDebut,f.dateFin,f.codeAction FROM objectifs o JOIN objectifsstage os ON (os.idObjectif = o.objectif) JOIN fap f ON (f.idFAP = o.idFap) JOIN fonctions fc ON (os.idFonction = fc.idFonction) JOIN stage s On (f.idStage = s.idStage) JOIN stagiaire st ON (f.idStagiaire = st.idStagiaire) WHERE f.idStage IN({$stages}) AND os.idFonction IN({$fonctions}) AND f.idSite = ".$site->_id." AND st.service IN({$services2}) AND st.equipe IN({$equipes2}) AND st.tranche IN({$tranches2}) ORDER BY f.idStage, fc.idFonction, os.idObjectif");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			$dateDebutfapExplode = explode("/", $stagiaireFetch->dateDebut);
			$dateDebutfapFomat = intval($dateDebutfapExplode[2] . $dateDebutfapExplode[1] . $dateDebutfapExplode[0]);

			$dateFinfapExplode = explode("/", $stagiaireFetch->dateFin);
			$dateFinfapFomat = intval($dateFinfapExplode[2] . $dateFinfapExplode[1] . $dateFinfapExplode[0]);

			if ($dateDebutfapFomat >= $dateDebutFomat && $dateFinfapFomat <= $dateFinFomat){
				array_push($listFaps, $stagiaireFetch);
			}
		} 

		return $listFaps;
	}
}
?>
