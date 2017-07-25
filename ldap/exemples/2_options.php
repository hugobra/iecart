<?php

include_once("../LdapAuth.php");

// positionnement des options pour l'authentification
// le minimum est de saisir les infos de connexion du compte applicatif (dn et password)
// ici on saisie toutes les options possibles
$options = array(
	// liste des serveurs LDAP sur lesquels on doit tenter la connexion pour authentification. L'ordre est important
	'servers' 			=> array('recette-ldap-gardian.edf.fr'),
	'bind_dn'			=> "a_definir",
	'bind_pwd'			=> "a_definir", 
	// utilisation de LDAPS au lieu de LDAP
	'use_ldaps'			=> true,
	// base de recherche dans l'annuaire pour la recherche d'utilisateur
	'searchBase'		=> 'ou=people,dc=gardiansesame',
	// attributs devant �tre retourn�s lors de la recherche d'utilisateur
	'searchAttributes'	=> array('cn', 'sn', 'mail', 'uid'),
	// fermeture automatique de la connection LDAP � la fin de l'authentification (utile si plusieurs authentification dans le script)
	'closeConnection'	=> true,
	// d�lai maximum de recherche LDAP (pour la recherche de l'utilisateur pour r�cup�ration du DN)
	'timelimit'			=> 1
);

try {
	$ldapAuth =& new LdapAuth($options);
	$ldapAuth->authenticateUser('user_a_definir', 'mdp_a_definir');
	echo "Utilisateur authentifi�<br />";

	echo 'Email : ' . $ldapAuth->getEmail() . '<br />';
	echo 'NNI : ' . $ldapAuth->getNNI() . '<br />';
} catch (LdapException $e) {
	echo $e->getMessage();
}


?>