<?php
include_once '../classes/stage.class.php';


if (isset($_GET['stage']) && isset($_GET['objectif']) && isset($_GET['fonction']) ){

$opss=Stage::getAllFromStageObjectifAndFonctionpourcréerstage($_GET['stage'],$_GET['objectif'],$_GET['fonction']);
}
exit(json_encode($opss));

?>