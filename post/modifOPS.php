<?php
include_once '../classes/OPS.class.php';


if (isset($_POST['id'])){
$ops = new OPS($_POST['id']);
$ops->modifierOPS($_POST['nom']);
}
?>