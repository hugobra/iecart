<?php
include_once("../LdapAuth.php");

// positionnement des options pour l'authentification
// dans le cas d'un chargement via un fichier ini, seules les options inifile et configSection sont prises en compte
// toutes les autre directives de configuration sont ignorées
$options = array(
	'inifile'			=> 'conf.ini',
	'configSection'		=> 'developpement'
);

try {
	$ldapAuth =& new LdapAuth($options);
	$ldapAuth->authenticateUser('user_a_definir', 'mdp_a_definir');
	echo "Authentification réussie";
} catch (LdapException $e) {
	echo $e->getMessage();
}

?>