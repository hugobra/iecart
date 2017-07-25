<?php
include_once '../classes/stage.class.php';


if (isset($_POST['stage']) && isset($_POST['objectif']) && isset($_POST['fonction']) ){

$ops=Stage::addOPS($_POST['stage'],$_POST['objectif'],$_POST['fonction'],$_POST['ops']);
}
?>