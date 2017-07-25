<?php

include_once("../LdapAuth.php");

// positionnement des options pour l'authentification
// le minimum est de saisir les infos de connexion du compte applicatif (dn et password)
$options = array(
	'servers' 			=> array('recette-ldap-gardian.edf.fr'),
	'bind_dn'			=> "a_definir",
	'bind_pwd'			=> "a_definir"
);
try {
	$ldapAuth =& new LdapAuth($options);
	$ldapAuth->authenticateUser('user_a_definir', 'mdp_a_definir');

	echo 'Utilisateur authentifié<br />';
	echo 'NNI : ' . $ldapAuth->getNNI() . '<br />';
	echo 'Email : ' . $ldapAuth->getEmail() . '<br />';
} catch (LdapException $e) {
	echo $e->getMessage();
}


?>