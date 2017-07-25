<?php
include_once '../classes/user.class.php';

$nom = UserManager::getNamefromNNI($_GET['nni']);

exit($nom);
?>