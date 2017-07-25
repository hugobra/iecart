<?php
include_once 'user.class.php';
include_once 'site.class.php';
include_once 'service.class.php';
include_once 'equipe.class.php';
include_once 'tranche.class.php';
include_once 'fonction.class.php';
include_once 'stage.class.php';
include_once 'fap.class.php';
include_once 'rang.class.php';

/**
* Classe de stagiaire, héritée de la classe abstraite user
*/
class Stagiaire extends User
{
	function __construct(){
		// Récupère les paramtres et appelle le constrcuteur parent
		$args = func_get_args();
		call_user_func_array(array($this, 'parent::__construct'), $args);
	}

	/**
	* Permet d'obtenir les informations concernant un utilisateur
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		//$userQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE idStagiaire = " . $id);
		$userQuery = $bdd->_connexion->Query("SELECT st.*, e.idEquipe, e.nomEquipe, t.idTranche, t.nomTranche, s.idService, s.nomService, f.idFonction, f.nomFonction FROM stagiaire st LEFT JOIN equipes e ON (e.idEquipe = st.equipe) LEFT JOIN tranches t ON (t.idTranche = st.tranche) LEFT JOIN services s ON(st.service = s.idService) LEFT JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) WHERE idStagiaire = " . $id);
		if (!$userQuery){
			return false;
		}

		$userQuery->setFetchMode(PDO::FETCH_OBJ);
		$userFetch = $userQuery->fetch();

		$this->_site     = new Site($userFetch->idSite);
		$this->_nom      = $userFetch->nom;
		$this->_prenom   = $userFetch->prenom;
		$this->_service  = (!is_null($userFetch->service)) ? new Service($userFetch->service, $userFetch->nomService) : new Service(0, "Non défini");
		$this->_equipe   = (!is_null($userFetch->idEquipe)) ? new Equipe($userFetch->idEquipe, $userFetch->nomEquipe) : new Equipe(0, "Non défini");
		$this->_tranche  = (!is_null($userFetch->idEquipe)) ? new Tranche($userFetch->idTranche, $userFetch->nomTranche) : new Tranche(0, "Non défini");
		$this->_fonction = (!is_null($userFetch->idFonction)) ? new Fonction($userFetch->idFonction, $userFetch->nomFonction) : new Fonction(0, "Non défini");
		$this->_nni      = $userFetch->nni;
		$this->_dua      = $userFetch->DUA;

		return true;
	}

	/**
	* Créer le stagiaire en base de donnée
	*/
	public function creer(){
		$bdd = new BDD();

		$idSite   = $bdd->_connexion->Quote($this->_site->_id);
		$nom      = $bdd->_connexion->Quote($this->_nom);
		$prenom   = $bdd->_connexion->Quote($this->_prenom);
		$service  = $bdd->_connexion->Quote($this->_service);
		$equipe   = $bdd->_connexion->Quote($this->_equipe);
		$tranche  = $bdd->_connexion->Quote($this->_tranche);
		$fonction = $bdd->_connexion->Quote($this->_fonction);
		$nni      = $bdd->_connexion->Quote($this->_nni);
		$dua      = $bdd->_connexion->Quote($this->_dua);

		if (!empty($this->_nni)){
			$nnitest = $bdd->_connexion->query("SELECT * FROM stagiaire WHERE nni = {$nni}");
			$nnitestFetch=$nnitest->fetch();
			if ($nnitestFetch){
				$bdd->_connexion->query("INSERT INTO stagiairesite (idStagiaire, idSite) VALUES(".$nnitestFetch['idStagiaire'].", {$idSite})");
				$bdd->_connexion->query("INSERT INTO fonction(idStagiaire, nom) VALUES (".$nnitestFetch['idStagiaire'].", {$fonction})");
				return $nnitestFetch['idStagiaire'];
			}
		}

		$bdd->_connexion->query("INSERT INTO stagiaire(idSite, nom, prenom, service, equipe, tranche, fonctionActuelle, nni, DUA) VALUES({$idSite}, {$nom}, {$prenom}, {$service}, {$equipe}, {$tranche}, {$fonction}, {$nni},{$dua})");

		$dernierStagiaire = $bdd->_connexion->query('SELECT idStagiaire FROM stagiaire ORDER BY idStagiaire DESC');
		$monIdStagiaire=$dernierStagiaire->fetch();

		$idStagiaire = $bdd->_connexion->Quote($monIdStagiaire['idStagiaire']);

		$bdd->_connexion->query("INSERT INTO stagiairesite (idStagiaire, idSite) VALUES({$idStagiaire}, {$idSite})");
		$bdd->_connexion->query("INSERT INTO fonction(idStagiaire, nom) VALUES ({$idStagiaire}, {$fonction})");

		return $monIdStagiaire['idStagiaire'];
	}

	/**
	* Modifier les informations du stagiaire
	*/
	public function modifier($nom, $prenom, $service, $equipe, $tranche, $fonction, $nni = '',$dua){
		$bdd = new BDD();

		$nom      = $bdd->_connexion->Quote($nom);
		$prenom   = $bdd->_connexion->Quote($prenom);
		$service  = $bdd->_connexion->Quote($service);
		$equipe   = $bdd->_connexion->Quote($equipe);
		$tranche  = $bdd->_connexion->Quote($tranche);
		$fonction = $bdd->_connexion->Quote($fonction);
		$nni      = $bdd->_connexion->Quote($nni);
		$dua      = $bdd->_connexion->Quote($dua);

		$bdd->_connexion->query("UPDATE stagiaire SET nom = {$nom}, prenom = {$prenom}, service = {$service}, equipe = {$equipe}, tranche = {$tranche}, fonctionActuelle = {$fonction}, nni = {$nni}, DUA = {$dua} WHERE idStagiaire = {$this->_id}");
	}

	/**
	* Ajouter une fonction au stagiaire
	*/
	public function addFonction($fonction){
		$bdd = new BDD();

		$fonction = $bdd->_connexion->Quote($fonction);

		$bdd->_connexion->query("INSERT INTO fonction(idStagiaire, nom) VALUES ({$this->_id}, {$fonction})");
	}

	/**
	* Obtenir l'id du stagiaire
	*/
	public function getId(){
		$bdd = new BDD();

		$idQuery = $bdd->_connexion->Query("SELECT idStagiaire FROM stagiaire WHERE nni = '" . $this->_nni . "'");
		$idQuery->setFetchMode(PDO::FETCH_OBJ);
		$idfetch = $idQuery->fetch();

		return $idfetch->idStagiaire;
	}

	/**
	* Obtenir toutes les FAP du stagiaire
	*/
	public function getFaps(){
		$bdd = new BDD();

		$stages = array();

		$stageQuery = $bdd->_connexion->Query("SELECT * FROM fap WHERE idStagiaire = " . $this->_id . " ORDER BY idFAP DESC");
		if (!$stageQuery){
			return false;
		}
		$stageQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stageFetch = $stageQuery->fetch()){
			array_push($stages, new FAP($stageFetch->idFAP));
		}

		return $stages;
	}

	/**
	* Obtenir toutes les fonctions qu'a occupé un stagiaire
	*/
	public function getFonctions(){
		$bdd = new BDD();

		$fonctions = array();

		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonction WHERE idStagiaire = " . $this->_id);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($fonctionFetch = $fonctionQuery->fetch()){
			array_push($fonctions, new Fonction($fonctionFetch->nom));
		}

		return $fonctions;
	}

	/**
	* Permet d'obtenir la premiere fonction du stagiaire
	* Code non retouché issu des anciens devs
	*/
	public function getFirstFonction(){
		$bdd = new BDD();

		$premiereOccurence=$bdd->_connexion->query('SELECT DISTINCT fap.dateCreation, fap.fonction FROM stage, fap, stagiaire WHERE fap.idStagiaire='.$_GET['id'].' AND fap.idStage=stage.idStage ORDER BY fap.dateCreation DESC');
		$premiereDate=$premiereOccurence->fetch();
		$datePremiere=$premiereDate['dateCreation'];
		return $premiereDate['fonction'];

		/*Récupération de la partie année de la première date*/
		$date_explosee = explode("-", $datePremiere);

		$annee = $date_explosee[0];
		/*$mois = $date_explosee[1];
		$jour = $date_explosee[2];*/

		$premiereAnnee=$annee;
	}

	/**
	* Permet d'obtenir la premiere année du stagiaire
	* Code non retouché issu des anciens devs
	*/
	public function getFirstYear(){
		$bdd = new BDD();

		$premiereOccurence=$bdd->_connexion->query('SELECT DISTINCT fap.dateCreation, fap.fonction FROM stage, fap, stagiaire WHERE fap.idStagiaire='.$_GET['id'].' AND fap.idStage=stage.idStage ORDER BY fap.dateCreation DESC');
		$premiereDate=$premiereOccurence->fetch();
		$datePremiere=$premiereDate['dateCreation'];

		/*Récupération de la partie année de la première date*/
		$date_explosee = explode("-", $datePremiere);

		$annee = $date_explosee[0];
		/*$mois = $date_explosee[1];
		$jour = $date_explosee[2];*/

		return $annee;
	}

	/**
	* Permet d'obtenir le premier site du stagiaire
	* Code non retouché issu des anciens devs
	*/
	public function getFirstSite(){
		$bdd = new BDD();

		$premiereOccurence=$bdd->_connexion->query('SELECT DISTINCT fap.dateCreation, fap.fonction, fap.idSite FROM stage, fap, stagiaire WHERE fap.idStagiaire='.$_GET['id'].' AND fap.idStage=stage.idStage ORDER BY fap.dateCreation DESC');
		$premiereDate=$premiereOccurence->fetch();
		$datePremiere=$premiereDate['idSite'];

		return $datePremiere;
	}

	/**
	* Permet d'obtenir tous les site sur lesquels le stagiaire est visible
	*/
	public function getAllSite(){
		$bdd = new BDD();

		$sites = array();

		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM stagiairesite ss JOIN site s ON (ss.idSite = s.idSite) WHERE ss.idStagiaire = " . $this->_id);
		$stagiaireQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			array_push($sites, new Site($stagiaireFetch->idSite));
		}

		return $sites;
	}

	/**
	* Permet de vérifier quer le stagiaire a bel et bien accès au site passé en paramètre
	*/
	public function isOnthisSite($site){
		$sites = $this->getAllSite();

		foreach ($sites as $siteATester) {
			if ($siteATester->_id == $site->_id){
				return true;
			}
		}

		return false;
	}

	/**
	* Verifier si le stagiaire a un profil sur un autre site
	*/
	public function hasProfileOnAnotherSite(){
		$bdd = new BDD();

		$stagiaireQuery = $bdd->_connexion->query("SELECT * FROM stagiairesite ss JOIN stagiaire s ON (ss.idStagiaire = s.idStagiaire) WHERE s.nni = '" . $this->_nni ."'");
		while ($stagiaireFetch = $stagiaireQuery->fetch()){
			return true;
		}

		return false;
	}

	/**
	* Demander la visiblité d'un profil pour un site
	*/
	public function askNewSite($site){
		$bdd = new BDD();

		$stagiaire = UserManager::getStagiaireFromNNI($this->_nni);

		$bdd->_connexion->query("INSERT INTO profilerequest (idStagiaire, idSite) VALUES(" . $stagiaire->_id . ", " . $site->_id . ")");

		return true;
	}

	/**
	* Ajouter un site a un stagiaire
	*/
	public function addSite($site){
		$bdd = new BDD();

		if (!$this->isOnthisSite($site)){
			$bdd->_connexion->query("INSERT INTO stagiairesite (idStagiaire, idSite) VALUES(".$this->_id.", ".$site->_id.")");
		}
	}

	/**
	* Supprimer la visibilité du profil sur un site
	*/
	public function removeSite($site){
		$bdd = new BDD();

		$bdd->_connexion->query("DELETE FROM stagiairesite WHERE idStagiaire = ".$this->_id." AND idSite = ".$site->_id);

		if ($this->_site->_id == $site->_id){
			$sites = $this->getAllSite();
			if (count($sites) > 0){
				$bdd->_connexion->query("UPDATE stagiaire SET idSite = '".$sites[0]->_id."' WHERE idStagiaire = ".$this->_id);
			}
		}
	}
	
	public function supprimerfonction($idstagiaire,$idfonction){
		$bdd = new BDD();

		$idstagiaire = $bdd->_connexion->Quote($idstagiaire);
		$idfonction = $bdd->_connexion->Quote($idfonction);

		$bdd->_connexion->Query("DELETE FROM fonction WHERE idStagiaire = {$idstagiaire} AND idFonction={$idfonction} ");
	}
	
	public function supprimerfonctionactuelle($stagiaire,$fonction,$fonctionhisto,$nouvellefonctionactuelle){
		$bdd = new BDD();

		$stagiaire = $bdd->_connexion->Quote($stagiaire);
		$fonction = $bdd->_connexion->Quote($fonction);
		$fonctionhisto = $bdd->_connexion->Quote($fonctionhisto);
		$nouvellefonctionactuelle= $bdd->_connexion->Quote($nouvellefonctionactuelle);
		$bdd->_connexion->Query("DELETE FROM fonction WHERE idStagiaire = {$stagiaire} AND idFonction={$fonctionhisto} ");
		$bdd->_connexion->Query("UPDATE stagiaire SET fonctionActuelle = {$nouvellefonctionactuelle} WHERE idStagiaire = {$stagiaire} ");
	}
	
	public function getFonctions2(){
		$bdd = new BDD();

		$fonctions = array();

		$fonctionQuery = $bdd->_connexion->Query("SELECT * FROM fonction WHERE idStagiaire = " . $this->_id);
		if (!$fonctionQuery){
			return false;
		}
		$fonctionQuery->setFetchMode(PDO::FETCH_OBJ);
		while ($fonctionFetch = $fonctionQuery->fetch()){
			array_push($fonctions, new Fonctionhisto($fonctionFetch->idFonction));
		}

		return $fonctions;
	}
}
?>