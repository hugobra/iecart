<?php
// Fichier de configuration relatif à la base de données

$production = false;

// Config de développement
if ($production == false){
	define('DB_HOST', "localhost");
	define('DB_NAME', "ecart");
	define('DB_USER', "root");
	define('DB_PASSWORD', "");

	
}else{
	
}




?>
