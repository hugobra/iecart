<?php
$host = "ldaps://dmztest-annuaire-gardiansesame.edf.fr:636";
$app_user = "uid=9TIFP001,ou=Applis,dc=gardiansesame";
$app_password = "Mmdp-3325";

$ds=ldap_connect($host);
echo "Le r&eacute;sultat de connexion est : ". $ds ."<br/>";

if (ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
	echo "Utilisation de LDAPV" . 3 . '<br/>';
} else {
	echo "Impossible de modifier la version du protocole en LDAPV" . 3 . '<br/>';
}

if ($ds) {
	$r = @ldap_bind($ds, $app_user, $app_password);
	echo "DS: " . $ds . ", USER: " . $app_user . "PASS: " . $app_password . "<br>";
	
	$rok = ($r) ? 'Réussie' : 'Echouée';
	echo "Authentification avec le compte applicatif : ". $rok . "<br/>";
}
?>