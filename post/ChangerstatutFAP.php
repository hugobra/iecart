<?php
include_once '../classes/fap.class.php';

$bdd = new BDD();

if (isset($_POST['idFap'])){
 $fapstatutchangée = new FAP($_POST['idFap']);
	if (isset($_POST['statut'])){
 $fapstatutchangée->changerstatut($_POST['statut']);		
 
}
}
?>