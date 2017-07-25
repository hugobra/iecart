<?php
session_start();

include_once '../classes/user.class.php';
include_once '../classes/rang.class.php';

$rangAdmin = UserManager::getRangFromNNI($_POST['adminNni'], $_POST['site']);

if ($rangAdmin >= Rangs::SUPERADMIN){
	UserManager::changeRangFromNNIAndSite($_POST['nni'], $_POST['rang'], $_POST['site']);
}else if($rangAdmin < Rangs::SUPERADMIN && $rangAdmin >= Rangs::ADMINSITE) {
	if (UserManager::isNniOnSite($_POST['nni'], $_POST['site'])){
		UserManager::changeRangFromNNIAndSite($_POST['nni'], $_POST['rang'], $_POST['site']);
	}
}
?>