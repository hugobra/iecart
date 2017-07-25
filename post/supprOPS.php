<?php
include_once '../classes/stage.class.php';


if (isset($_POST['id']) ){
//$ops=new Stage($_POST['id']);
$ops=Stage::supprimerOPS($_POST['id']);
}
?>