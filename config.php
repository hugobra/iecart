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

// Config de production
// define('DB_HOST', "10.122.1.39:3308");
// define('DB_NAME', "test");
// define('DB_USER', "root");
// define('DB_PASSWORD', "sUcyfSHWD#?a");

// define('LDAP_HOST', "ldaps://dmztest-annuaire-gardiansesame.edf.fr:636");
// define('LDAP_RACINE', "ou=people,dc=gardiansesame");
// define('LDAP_ROOTDN', "uid=9TIFP001,ou=Applis,dc=gardiansesame");
// define('LDAP_PASSWORD', "Mmdp-3325");
?>
