<?php
include_once '../config.php';
include_once '../classes/stagiaire.class.php';

/*
Démarrage de session
*/
session_cache_limiter('private, must-revalidate');
session_start();

$stagiaire = new Stagiaire($_GET['id']);
$faps = $stagiaire->getFaps();
$fonctions = $stagiaire->getFonctions2();
$sites = $stagiaire->getAllSite();
$allSites = Site::getAll();

$site = new Site($_SESSION['site']);

// Verification que le site a les droits sur le stagiaire pour afficher le profil
$goodSite = false;
if ($site->_id == $stagiaire->_site->_id){
	$goodSite = true;
}
foreach ($sites as $siteTest) {
	if ($siteTest->_id == $site->_id){
		$goodSite = true;
	}
}
if(!$goodSite){
	exit("Ce site n'a pas les droits d'afficher ce profil <a href='/route/stagiaires.php'>Retour liste stagiaires</a>");
}

if(isset($_GET['siteSuppr'])){
	$stagiaire->removeSite(new Site($_GET['siteSuppr']));

	if ($_SESSION['site'] == $_GET['siteSuppr']){
		echo"<script>window.location='stagiaires.php';</script>";
	}else{
		echo"<script>window.location='ficheStagiaire.php?id=".$_GET['id']."';</script>";
	}
}

if(isset($_POST['addSite'])){
	$stagiaire->addSite(new Site($_POST['addSite']));

	echo"<script>window.location='ficheStagiaire.php?id=".$_GET['id']."';</script>";
}

/*C'est à partir de ces deux valeurs (premiereAnnee et fonctionPremiere) que nous allons initialiser notre fonction de démarquement*/
$premiereAnnee = $stagiaire->getFirstYear();
$fonctionPremiere = $stagiaire->getFirstFonction();
$sitePremier = $stagiaire->getFirstSite();

try
{
	/* Connexion à la BD*/
  	$bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	
	/*Demande de confirmation d'une suppression d'une FAP*/
	if(isset($_GET['idSuppr']) && !isset($_GET['conf'])){
	?>
	
	<script>
		//Il faut écrire oui en toutes lettres pour confirmer la suppression
	var suppression=prompt('Voulez-vous confirmer la suppression (oui/non) ?');
	 if(suppression=="oui"){
		window.location='ficheStagiaire.php?id=<?php echo $_GET['id']; ?>&idSuppr=<?php echo $_GET['idSuppr']; ?>&conf=true';
	 } 
	else
	window.location='ficheStagiaire.php?id=<?php echo $_GET['id']; ?>';
	</script>
	
	<?php
	}
	/*S'il y a eu confirmation, suppression de la FAP correspondante*/
	if(isset($_GET['conf'])){
		//echo $_GET['idSuppr'];
		$bdd->query('DELETE FROM fap WHERE idFap='.$_GET['idSuppr']);
		echo"<script>window.location='ficheStagiaire.php?id=".$_GET['id']."';</script>";
	}

  /*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" "){


	include("../tpl/pages/fiche_stagiaire.tpl.html");}
else
	include("connexion.php");
}

catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}

?>
