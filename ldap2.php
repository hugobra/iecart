<?php
include_once 'classes/ldap.class.php';

$uid = "TEST08EP";

echo "Connexion...<br>";

$ds=ldap_connect(LDAP_HOST);
if ($ds==1)
    {
	// On s'authentifie sur le serveur LDAP
	$r=ldap_bind($ds, LDAP_ROOTDN, LDAP_PASSWORD);

	$sr = ldap_search($ds,"ou=People,dc=gardiansesame", "uid=TEST08EP");
	echo "Le r&eacute;sultat de la recherche est : ".$sr."<br/>";
	$nb_entrees = ldap_count_entries($ds,$sr);
	echo "Entrees: " . $nb_entrees . "<br/>";

	$compte = ldap_get_entries($ds, $sr);
	//var_dump($compte[0]['sn'][0]);
	$nom = $compte[0]['sn'][0];

	/*
	$dn = "o=My Company, c=US";
	$filter="(|(uid=*))";

	$sr=ldap_search($ds, LDAP_RACINE, $filter);

	$info = ldap_get_entries($ds, $sr);

	echo $info["count"]." entries returned\n";
	var_dump($info);

	*/

	echo "Déconnexion...<br>";

	ldap_close($ds);

} else {
	echo  "Impossible de se connecter au serveur LDAP";
}
?>
