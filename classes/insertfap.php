<?php
include 'bdd.class.php';

$bdd = new BDD();
for ($i=0; $i < 1000; $i++) { 
	$bdd->_connexion->query("INSERT INTO stagiaire (idSite, nom, prenom, service, equipe, tranche, fonctionActuelle, nni) VALUES(1, 'Jean', 'Michel', 1, 7, 1, 2, 'X99999')");
}
?>