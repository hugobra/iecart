<?php
include_once '../classes/objectif.class.php';

$objectif = new Objectif($_POST['id']);
$objectif->modifierNom($_POST['nom']);
?>