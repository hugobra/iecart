<?php
/**
 * LdapAuth_Config
 * 
 * @package LdapAuth
 */

/**
 */
include_once(LDAPAUTH_PATH . "Gof/Subject.php");

/**
 * LdapAuth_Config
 * 
 * @package LdapAuth
 * @author guennoc-nic 
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public 
 */
class LdapAuth_Config
{
	/**
	 * Fermeture automatique de la connection LDAP
	 * 
	 * @access public 
	 * @var boolean 
	 */
	public $closeConnection = false;

	/**
	 * Liste des serveurs LDAP disponibles pour authentification
	 * 
	 * @access public 
	 * @var array 
	 */
	public $servers = array('recette-ldap-gardian.edf.fr');

	/**
	 * DN du compte applicatif
	 * 
	 * @access public 
	 * @var string 
	 */
	public $bind_dn = '';

	/**
	 * DN du compte applicatif
	 * 
	 * @access public 
	 * @var string 
	 */
    public $bind_pwd = '';

	/**
	 * Indique l'utilisation ou non de LDAPS
	 * 
	 * @access public 
	 * @var boolean 
	 */
	public $use_ldaps = true;

	/**
	 * Base de recherche dans l'annuaire LDAP pour la recherche des utilisateurs
	 * 
	 * @access public 
	 * @var string 
	 */
	public $searchBase = 'ou=people,dc=gardiansesame';

	/**
	 * Liste des attributs � retourner lors de la recherche utilisateur
	 * Rq : le Dn est retourn� m�me s'il n'est pas pr�sent dans la liste
	 * 
	 * @access public 
	 * @var string 
	 */
	public $searchAttributes = array('cn', 'sn', 'mail', 'uid');

	/**
	 * Constructor
	 * 
	 * @access protected 
	 */
	protected function __construct()
	{
	}

	/**
	 * Pattern SINGLETON : retourne une instance LdapAuth_Config vierge
	 * 
	 * @access public 
	 * @return void 
	 */
	public function &getInstance()
	{
		static $instance = null;
		if (is_null($instance))
		{
			$instance = new LdapAuth_Config();
		}
		return $instance;
	}

	/**
	 * Permet de v�rifier la validit� des variables pass�es en param�tre
	 * Validit� = existence, non-vide (et �ventuellement des tests sp�cifiques suppl�mentaires)
	 * 
	 * @access public 
	 * @see checkValue
	 * @return void 
	 */
	public function checkValues($values = null)
	{
		if (is_null($values))
		{
			return false;
		}
		if (is_array($values) && !empty($values))
		{
			foreach($values as $value)
			{
				if (!$this->checkValue($value))
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Permet de v�rifier la validit� de la variable pass�e en param�tre
	 * Validit� = existence, non-vide (et �ventuellement des tests sp�cifiques suppl�mentaires)
	 * 
	 * @access public 
	 * @return void 
	 */
	public function checkValue($name = null)
	{
		if (is_null($name))
		{
			return false;
		}
		if (isset($this->$name) && !empty($this->$name))
		{
			return true;
		}
		return false;
	}

	/**
	 * Permet de charger un fichier de configuration au format INI
	 * Il est possible de sp�cifier une section particuli�re dans ce fichier pour ne r�cup�rer qu'une configuration particuli�re
	 * Cela permet de cr�er divers environnements LDAP (production, dev, ...)
	 * 
	 * @param  $filename 
	 * @param boolean $section 
	 * @access private 
	 * @return boolean true ou ojbect LdapAuth_Error
	 */
	private function loadIniFile($filename, $section = false)
	{ 
		// si le fichier existe et est lisible
		if (file_exists($filename) && is_readable($filename))
		{ 
			// s'il est pr�cis� de lire une section (environnement) particuli�re
			if (!empty($section))
			{ 
				// on parse le fichier ini
				$data = parse_ini_file($filename, true); 
				// si la section demand�e est disponible
				if (isset($data[$section]))
				{ 
					// on positionne les options
					$this->setOptions($data[$section]);
				}
			}
			else
			{ 
				// si aucune section n'est sp�cifi�e, on parse le fichier ini en tenant compte des sections
				$data = parse_ini_file($filename, true); 
				// puis on r�cup�re la premi�re section trouv�e
				$section = each($data); 
				// dont on positionne ensuite les options.
				// on sp�cifie $section[1] car each renvoie un tableau mixte vecteur/hashtable (cf doc PHP)
				$this->setOptions($section[1]);
			}
			return true;
		}
		else
		{
			return LdapException::raiseError("inifile_not_found");
		}
	}

	/**
	 * Positionne les options de configuration pass�es en param�tre
	 * 	 Si la directive inifile est pr�sente, on ne traite pas les autres (comportement exclusif)
	 * 
	 * @param array $data 
	 * @access public 
	 * @return void 
	 */
	public function setOptions($data = null)
	{
		// si les donn�es de conf ne sont pas nulles
		if (is_array($data) && !empty($data))
		{
			// on teste si l'option inifile est pr�sente => chargement conf via fichier ini
			if (isset($data['inifile']))
			{ 
				// on teste si on a pr�cis� une conf particuli�re dans le fichier ini (i.e. section)
				$configSection = (isset($data['configSection'])) ? $data['configSection'] : false; 
				// on charge le fichier ini
				$this->loadIniFile($data['inifile'], $configSection);
			}
			else
			{ 
				// s'il n'y a pas d'option inifile, on charge les options pr�sentes dans le tableau
				// c'est un choix conceptuel : on ne mixe pas le chargement par tableau et par fichier ini
				foreach($data as $name => $value)
				{
					if (isset($this->$name))
					{
						switch ($name)
						{
							case 'servers':
								$this->$name = (is_string($value)) ? explode(',', $value) : $value;
								break;
							default:
								$this->$name = $value;
						} // switch
					}
				}
			}
		}
	}
}

?>
