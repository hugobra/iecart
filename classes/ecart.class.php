<?php
include_once 'bdd.class.php';
include_once 'site.class.php';
include_once 'user.class.php';
include_once 'missions.class.php';
include_once 'statut.class.php';


/**
* Permet de gérer les éléments relatifs aux écarts
*/
class ECART
{
	public $_id;
	public $_émetteur;
	public $_mission;
	public $_récepteur;
	public $_titre;
	public $_description;
	public $_propositiontraitement;
	public $_statut;
	public $_dateCréation;
	public $_traitement;
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

		$siteQuery = $bdd->_connexion->Query("SELECT * FROM ecarts WHERE idEcart = " . $id);
		if (!$siteQuery){
			return false;
		}
		$siteQuery->setFetchMode(PDO::FETCH_OBJ);
		$siteFetch = $siteQuery->fetch();

		$this->_émetteur = new UserManager( $siteFetch->Emetteur);
		$this->_mission = new Mission($siteFetch->Mission);
		$this->_récepteur = new UserManager ($siteFetch->Recepteur);
		$this->_titre = $siteFetch->Titre;
		$this->_description = $siteFetch->Description;
		
		$this->_propositiontraitement = $siteFetch->Propositiontraitement;
		$this->_statut = new Statut($siteFetch->Statut);
		$this->_dateCréation = $siteFetch->DateCreation;
		$this->_traitement = $siteFetch->Traitement;
		$this->_site = new Site($siteFetch->Site);
		

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
		$listEcart = array();

		$bdd = new BDD();

		$stagiaireQuery =$bdd->_connexion->query("SELECT idEcart FROM ecarts WHERE Site={$site}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listEcart, new Ecart($stagiaireFetch->idEcart));
		}

		return $listEcart;
	}

	/**
	* Obtenir les écarts d'un utilisateur -> son dashboard émetteur
	*/
	public static function getAllFromSiteForUserEmetteur($user){
		$listEcart = array();

		$bdd = new BDD();
		$user = $bdd->_connexion->Quote($user->_id);
		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM ecarts WHERE Emetteur={$user}");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listEcart, new Ecart($stagiaireFetch->idEcart));
		}

		return $listEcart;
	}
	
	/**
	* Obtenir les écarts d'un utilisateur -> son dashboard émetteur
	*/
	public static function getAllFromSiteForUserRécepteur($user){
		$listEcart = array();

		$bdd = new BDD();
		$user = $bdd->_connexion->Quote($user->_id);
		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM ecarts WHERE Recepteur={$user} ");
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($listEcart, new Ecart($stagiaireFetch->idEcart));
		}

		return $listEcart;
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
	public static function create($id, $émetteur, $mission, $récepteur, $titre, $description, $propotraitement, $dateCreation, $site){
		$bdd = new BDD();
		
		$id = $bdd->_connexion->Quote($id);
		$mission = $bdd->_connexion->Quote($mission);
		$émetteur = $bdd->_connexion->Quote($émetteur);
		$récepteur = $bdd->_connexion->Quote($récepteur);
		$titre = $bdd->_connexion->Quote($titre);
		$description = $bdd->_connexion->Quote($description);
		$propotraitement = $bdd->_connexion->Quote($propotraitement);
		$dateCreation = $bdd->_connexion->Quote($dateCreation);
		$site = $bdd->_connexion->Quote($site->_id);
		

		//echo("INSERT INTO fap(idFAP, idStage, idStagiaire, idSite, codeAction, codeSession, dateCreation, Formateur, fonction, dateDebut, dateFin) VALUES({$id}, {$stage}, {$stagiaire}, {$site}, {$codeAction}, {$codeSession}, {$dateCreation}, {$formateur}, {$fonction}, {$dateDebut}, {$dateFin})");

		$reqEcart=$bdd->_connexion->Query("INSERT INTO ecarts(idEcart, Emetteur, Mission, Recepteur, Titre, Description, Propositiontraitement, Statut, DateCreation, Site) VALUES({$id}, {$émetteur}, {$mission}, {$récepteur}, {$titre}, {$description}, {$propotraitement}, 1 , {$dateCreation}, {$site})")	;
		
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
	public static function getNewEcartNumber(){
		$bdd = new BDD();

		$Ecarts=$bdd->_connexion->query('SELECT * FROM ecarts ORDER BY idEcart DESC');
		$Ecart=$Ecarts->fetch();
		$idEcart=$Ecart['idEcart']+1;

		return $idEcart;
	}

	/**
	* Modifier un écart existant
	*/
	public function modifier($émetteur, $mission, $récepteur, $titre, $description, $propotraitement, $dateCreation, $site){
		$bdd = new BDD();
		
		$mission = $bdd->_connexion->Quote($mission);
		$émetteur = $bdd->_connexion->Quote($émetteur);
		$récepteur = $bdd->_connexion->Quote($récepteur);
		$titre = $bdd->_connexion->Quote($titre);
		$description = $bdd->_connexion->Quote($description);
		$propotraitement = $bdd->_connexion->Quote($propotraitement);
		$dateCreation = $bdd->_connexion->Quote($dateCreation);
		$site = $bdd->_connexion->Quote($site->_id);

		$reqFAP = $bdd->_connexion->Query("UPDATE ecarts SET Emetteur = {$émetteur}, Mission = {$mission}, Recepteur= {$récepteur}, Titre= {$titre}, Description= {$description}, Propositiontraitement= {$propotraitement}, DateCreation={$dateCreation},Site={$site}  WHERE idEcart = {$this->_id}");
	
		
	}

	
	public function traiter($traitement){
		$bdd = new BDD();
		
		$traitement = $bdd->_connexion->Quote($traitement);
		

		$reqFAP = $bdd->_connexion->Query("UPDATE ecarts SET Traitement= {$traitement}, Statut=2  WHERE idEcart = {$this->_id}");
	
		
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
