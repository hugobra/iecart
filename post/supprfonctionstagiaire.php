<?php
include_once '../classes/stagiaire.class.php';


if (isset($_POST['idstagiaire']) && isset($_POST['idfonction']) ){

$fonctionsuppr=Stagiaire::supprimerfonction($_POST['idstagiaire'],$_POST['idfonction']);
}

if (isset($_POST['stagiaire']) && isset($_POST['fonction']) && isset($_POST['fonctionhisto']) && isset($_POST['nouvellefonctionactuelle']) ){

$fonctionasuppr=Stagiaire::supprimerfonctionactuelle($_POST['stagiaire'],$_POST['fonction'],$_POST['fonctionhisto'],$_POST['nouvellefonctionactuelle']);
}
?>