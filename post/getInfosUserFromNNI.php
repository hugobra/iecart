<?php
include_once '../classes/stagiaire.class.php';

$bdd = new BDD();

if (empty($_GET['nni'])){
	exit();
}

$stagiaire = UserManager::getStagiaireFromNNI($_GET['nni']);
if (!$stagiaire){
	exit();
}
$sites = $stagiaire->getAllSite();

//$userQuery = $bdd->_connexion->Query("SELECT * FROM stagiaire WHERE nni = '" . $_GET['nni'] ."'");
$userQuery = $bdd->_connexion->Query("SELECT DISTINCT * FROM stagiaire st JOIN equipes e ON (e.idEquipe = st.equipe) JOIN tranches t ON (t.idTranche = st.tranche) JOIN services s ON(st.service = s.idService) JOIN fonctions f ON(f.idFonction = st.fonctionActuelle) WHERE st.nni = '".$_GET['nni']."'");
if (!$userQuery){
	exit();
}
$userQuery->setFetchMode(PDO::FETCH_OBJ);
$userFetch = $userQuery->fetch();
if (!$userFetch){
	exit();
}

$infos = array('nom' => $userFetch->nom, 'prenom' => $userFetch->prenom, 'service' => $userFetch->service, 'equipe' => $userFetch->equipe, 'tranche' => $userFetch->tranche, 'fonction' => $userFetch->fonctionActuelle, 'serviceNom' => $userFetch->nomService, 'equipeNom' => $userFetch->nomEquipe, 'trancheNom' => $userFetch->nomTranche, 'fonctionNom' => $userFetch->nomFonction, 'sites' => $sites);

exit(json_encode($infos));
?>