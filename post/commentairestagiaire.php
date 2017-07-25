<?php
include_once '../classes/fap.class.php';

$bdd = new BDD();

if (isset($_POST['idFap'])){
	$fapACommenter = new FAP($_POST['idFap']);
	if (isset($_POST['comment'])){
		$fapACommenter->commenter($_POST['comment']);
	}
}
?>