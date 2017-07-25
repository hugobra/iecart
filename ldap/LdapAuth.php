<?php
/**
 * LdapAuth
 * 
 * @package LdapAuth
 */
if (!extension_loaded('ldap')) {
	$msg = <<<NOTLDAP
<pre>
ERREUR:

L'extension LDAP n'est pas activ�e sur le serveur. 
Veuillez corriger ce probl�me afin de pouvoir utiliser l'authentification LDAP

Sous Windows
------------
V�rifiez que l'extension LDAP est bien d�clar�e dans le fichier php.ini :
extension=php_ldap.dll

Sous Linux/Solaris
------------------
En utilisant la commande phpinfo, v�rifiez que la ligne de compilation de PHP
comporte l'instruction --with-ldap=/logiciels/apache/apa_x.y.z/lib/openldap-a.b.c 

</pre>
NOTLDAP;
    die($msg);
}
/**
 */
define("LDAPAUTH_PATH", dirname(__FILE__) . "/LdapAuth/");

include_once(LDAPAUTH_PATH . "Config.php");
include_once(LDAPAUTH_PATH . "LdapException.php");
include_once(LDAPAUTH_PATH . "Gof/Subject.php");

/**
 * closeLdapConnection()
 *        Ferme la connection LDAP en fin de script
 * 
 * @param  $ds l'identifiant de connexion au serveur LDAP pour fermeture
 */
function closeLdapConnection($ds)
{
    if (is_resource($ds))
    {
        ldap_close($ds);
    } 
} 

/**
 * LdapAuth
 * 
 * @package LdapAuth
 * @author guennoc-nic 
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public 
 */
class LdapAuth extends Gof_Subject
{
	/**
	 * Repr�sente les options de configuration de l'objet LdapAuth
	 * 
	 * @access private 
	 * @var object 
	 */
	private $_config = null;

	/**
	 * Contient l'identifiant de connexion au serveur LDAP ou un objet LdapException si erreur
	 * 
	 * @access private 
	 * @var object 
	 */
	private $_ldapConn = null;

	/**
	 * Tableau contenant les informations relatives au dernier utilisateur authentifi�
	 * Contient notamment les valeurs suivantes :
	 * - l'adresse email
	 * - le NNI
	 * - le dn
	 * 
	 * @access public
	 * @var object 
	 */
	public $userInfo = array();

	/**
	 * Constructor
	 * 
	 * @access public 
	 */
	public function __construct($options = null)
	{
		$this->setConfig(LdapAuth_config::getInstance());
		$this->setOptions($options);
	}

	/**
	 * Permet de positionner les options de configuration de l'objet LdapAuth_Config
	 * 
	 * @param array $options array Tableau recensant les options
	 * @see LdapAuth_Config::setOptions()
	 * @access public 
	 * @return void 
	 */
	public function setOptions($options = null)
	{
		if (is_null($options))
		{
			return false;
		}
		$config = &$this->getConfig();
		$config->setOptions($options);
	} 

	/**
	 * Effectue l'authentification de l'utilisateur avec le nom et le mot de passe fournis
	 * 
	 * @param string $user l'adresse email ou le NNI fourni par l'utilisateur
	 * @param string $password le mot de passe fourni par l'utilisateur
	 * @access public 
	 * @return boolean Si l'utilisateur est authentifi� ou object Erreur
	 */
	public function authenticateUser($user, $password)
	{
		// on v�rifie que les param�tres fournis ne sont pas vides
		if (empty($user))
		{
			throw LdapException::raiseError("user_empty");
		} 
		if (empty($password))
		{
			throw LdapException::raiseError("password_empty");
		} 
		
		$this->notify('Tentative de r�cup�ration d\'une connexion', __CLASS__, __FUNCTION__, __LINE__); 
		// on r�cup�re l'identifiant de connexion (avec recherche du serveur si n�cessaire)
		$ldapConnexion = &$this->getLdapConn(); 
		// si aucune connexion possible on retourne une erreur
		if (is_object($ldapConnexion) && is_a($ldapConnexion, 'LdapException'))
		{
			$this->notify('Echec lors de la r�cup�ration d\'une connexion', __CLASS__, __FUNCTION__, __LINE__);
			throw $ldapConnexion;
		}
		// on r�cup�re le DN de l'utilisateur depuis son uid (pass� en param�tre)
		$this->notify("R�cup�ration du DN de l'utilisateur $user", __CLASS__, __FUNCTION__, __LINE__);
		$dn = $this->getUserDn($user);
		
		if (is_object($dn) && is_a($dn, 'LdapException'))
		{
			throw $dn;
		} 
		// si le DN r�cup�r� est correct, on essaie de se connecter avec celui-ci et le mot de passe fourni par l'utilisateur
		$this->notify("Tentative de connexion avec le DN $dn", __CLASS__, __FUNCTION__, __LINE__);
		$r = @ldap_bind($ldapConnexion, $dn, $password);
		
		if ($this->_config->closeConnection)
		{
			ldap_unbind($ldapConnexion);
			$null = null;
			$this->setLdapConn($null);
		}
		$config = $this->getConfig();
		if ($r)
		{
			$this->notify("Connexion r�ussie pour l'utilisateur $user", __CLASS__, __FUNCTION__, __LINE__);
			@ldap_bind($ldapConnexion, $config->bind_dn, $config->bind_pwd);
			return true;
		}
		
		@ldap_bind($ldapConnexion, $config->bind_dn, $config->bind_pwd); 
		// si le bind utilisateur a �chou�, on renvoie un objet LdapException
		$this->notify("Mot de passe incorrect pour l'utilisateur $user", __CLASS__, __FUNCTION__, __LINE__); 
		// ldap_close($ldapConnexion);
		throw LdapException::raiseError("password_incorrect");
	}

	/**
	 * Permet de r�cup�rer le DN d'un utilisateur en fonction de son uid
	 * Alimente �galement l'attribut userInfo pour r�cup�ration des informations de l'utilisateur
	 * 
	 * @param string $uid l'adresse email ou le NNI fourni par l'utilisateur
	 * @access private 
	 * @return string $dn le DN de l'utilisateur ou object LdapException : une erreur si l'utilisateur n'est pas trouv�
	 */
	public function getUserDn($uid = null)
	{ 
		// on recherche l'utilisateur sur son uid
		$searchFilter = "uid=$uid"; 
		// on d�termine la base de recherche de l'utilisateur dans l'arbre, avec une pr�dominance pour l'option de configuration 'searchBase' si elle existe
		$searchBase = (isset($this->_config->searchBase)) ? $this->_config->searchBase : 'c=fr';
		$ds = $this->getLdapConn(); 
		// on d�termine les attributs � r�cup�re (pr�dominance de l'option 'searchAttributes' si elle existe)
		$attributes = (isset($this->_config->searchAttributes)) ? $this->_config->searchAttributes : array('mail', 'dn');
		$timelimit = (isset($this->_config->timelimit)) ? $this->_config->timelimit : 5; 
		// lancement de la recherche
		$sr = ldap_search($ds, $searchBase, $searchFilter, $attributes, 0, 0, $timelimit); 
		// si le nombre d'entr�es trouv�s est inf�rieur � 1...
		if (ldap_count_entries($ds, $sr) < 1)
		{
			// alors on renvoie une erreur comme quoi l'utilisateur n'a pas �t� trouv�
			throw LdapException::raiseError("user_not_found");
		}
		// on r�cup�re le pointeur vers la premi�re entr�e...
		$entry = ldap_first_entry($ds, $sr); 
		// dont on r�cup�re le DN
		$dn = ldap_get_dn($ds, $entry);
		
		// R�cup�ration des entr�es trouv�es lors de la recherche
		$userEntries = ldap_get_entries($ds , $sr);

		// alimentation de l'attribut de classe userinfo pour sauvegarder les informations de l'utilisateur
		$this->userInfo['NNI'] = isset($userEntries[0]['uid'][0]) ? $userEntries[0]['uid'][0] : null;
		$this->userInfo['email'] = isset($userEntries[0]['mail'][0]) ? $userEntries[0]['mail'][0] : null;
		$this->userInfo['dn'] = $dn;
		$this->userInfo['cn'] = $userEntries[0]['cn'][0];
		ldap_free_result($sr); 
		// si le DN est au bon format...
		if (is_string($dn) && !empty($dn))
		{
			$this->notify("DN de l'utilisateur $uid --> $dn", __CLASS__, __FUNCTION__, __LINE__); 
			// on le retourne � la fonction appelante...
			return $dn;
		}
		// sinon on retourne une erreur comme quoi l'utilisateur n'a pas �t� trouv�
		return LdapException::raiseError("user_not_found");
	}

	/**
	 * Retourne le NNI de la derni�re personne authentifi�e
	 * @access public
	 * @return string le NNI de la derni�re personne authentifi�e
	 **/
	public function getNNI(){
		return $this->userInfo['NNI'];
	}
	
	/**
	 * Retourne l'adresse email de la derni�re personne authentifi�e
	 * @access public
	 * @return string l'adresse email de la derni�re personne authentifi�e
	 **/
	public function getEmail(){
		return $this->userInfo['email'];
	}
	
	/**
	 * Retourne le CN de la derni�re personne authentifi�e
	 * @access public
	 * @return string le CN de la derni�re personne authentifi�e
	 **/
	public function getCN(){
		return $this->userInfo['cn'];
	}

	/**
	 * Retourne le DN de la derni�re personne authentifi�e
	 * @access public
	 * @return string le DN de la derni�re personne authentifi�e
	 **/
	public function getDN(){
		return $this->userInfo['dn'];
	}
	
	/**
	 * Retourne un identifiant de connection LDAP en tenant compte de la liste des serveurs LDAP sp�cifi�e dans la configuration. Permet la bascule automatique en cas d'indisponibilit� d'un des serveurs
	 * 
	 * @access private 
	 * @return ressource $ds l'identifiant de connexion LDAP au serveur ou object LdapException : une erreur si aucune connexion n'est possible
	 */
	private function &getLdapConnection()
	{
		// r�cup�ration de la configuration
		$config = $this->getConfig();
		
		if (!$config->checkValues(array('bind_dn', 'bind_pwd')))
		{
			$this->notify("Les informations de connexion applicative sont incorrectes", __CLASS__, __FUNCTION__, __LINE__);
			throw LdapException::raiseError("invalid_application_credentials");
		}
		// on d�termine si on se connecte en ldap ou ldaps (option 'use_ldaps')
		$protocol = (isset($config->use_ldaps) && $config->use_ldaps) ? 'ldaps' : 'ldap';
		
		$servers = $config->servers; 
		// si la liste des serveurs disponibles pour connexion n'est pas vide...
		if (is_array($servers) && !empty($servers))
		{ 
			// on boucle sur chacun d'entre eux...
			foreach($servers as $server)
			{
				$url = "$protocol://$server";
				// on essaie de se connecter sur l'url du serveur (protocol + adresse)
				$ds = ldap_connect($url);
				if (is_resource($ds))
				{   
					// on essaie de s'authentifier avec les infos de connexion applicatives
					$r = @ldap_bind($ds, $config->bind_dn, $config->bind_pwd);
					$this->notify("Essai de connexion sur $url", __CLASS__, __FUNCTION__, __LINE__); 
					// si la connexion est r�ussi�
					if ($r)
					{
						$this->notify("Connexion R�ussie sur $url", __CLASS__, __FUNCTION__, __LINE__);
						if (!$this->_config->closeConnection)
						{
							// on enregistre l'identifiant de connexion pour fermeture automatique � la fin de l'ex�cution du script
							register_shutdown_function('closeLdapConnection', $ds);
						}
						return $ds;
					}
					$this->notify("Connexion �chou�e sur $url", __CLASS__, __FUNCTION__, __LINE__);
				}
				ldap_close($ds);
			}
			$this->notify("Aucune connexion disponible parmi les serveurs " . implode(',', $servers), __CLASS__, __FUNCTION__, __LINE__);
		}
		// si aucune connexion n'a �t� trouv�e on renvoie un objet LdapException
		throw LdapException::raiseError("no_connection");
	}
	// GETTER / SETTER
	/**
	 * Retourne la configuration
	 * 
	 * @access public 
	 * @return Object 
	 */
	public function &getConfig()
	{
		return $this->_config;
	}

	/**
	 * Positionne la configuration
	 * 
	 * @access public 
	 * @return void 
	 */
	public function setConfig(&$newValue)
	{
		$this->_config = &$newValue;
	}

	/**
	 * Retourne l'identifiant de connexion
	 * 
	 * @access public 
	 * @return Object 
	 */
	public function &getLdapConn()
	{
		if (is_null($this->_ldapConn))
		{
			$this->_ldapConn = &$this->getLdapConnection();
		}
		return $this->_ldapConn;
	}

	/**
	 * Positionne l'identifiant de connexino
	 * 
	 * @access public 
	 * @return void 
	 */
	public function setLdapConn(&$newValue)
	{
		$this->_ldapConn = &$newValue;
	}
}

?>
