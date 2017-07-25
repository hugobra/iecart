<?php

include_once("../LdapAuth.php");

// on crée deux instances de type Oberver
// une première effectue un log dans un tableau PHP
include_once(LDAPAUTH_PATH . "Gof/Observer/ArrayLogger.php");
$observerArray = new Observer_ArrayLogger();

// la deuxième effectue un log dans un fichier
// elle nécessite que l'on lui passe en paramètre l'emplacement du fichier de log
$options = array('filename' => '/tmp/ldaps.log');
include_once(LDAPAUTH_PATH . "Gof/Observer/FileLogger.php");
$observerFile = new Observer_FileLogger($options);

// positionnement des options pour l'authentification
// le minimum est de saisir les infos de connexion du compte applicatif (dn et password)
$options = array(
	'servers' 			=> array('recette-ldap-gardian.edf.fr'),
	'bind_dn'			=> "a_definir",
	'bind_pwd'			=> "a_definir",
);

try {
	$ldapAuth =& new LdapAuth($options);

	$ldapAuth->attach('arrayLog', $observerArray);
	$ldapAuth->attach('fileLog', $observerFile);

	$ldapAuth->authenticateUser('user_a_definir', 'mdp_a_definir');
	echo "Utilisateur authentifié<br />";
} catch (LdapException $e) {
	echo $e->getMessage();
}

echo "<pre>";
print_r($observerArray->getLog());
echo "</pre>";
?>