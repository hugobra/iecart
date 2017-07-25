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
	 * Liste des attributs à retourner lors de la recherche utilisateur
	 * Rq : le Dn est retourné même s'il n'est pas présent dans la liste
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
	 * Permet de vérifier la validité des variables passées en paramètre
	 * Validité = existence, non-vide (et éventuellement des tests spécifiques supplémentaires)
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
	 * Permet de vérifier la validité de la variable passée en paramètre
	 * Validité = existence, non-vide (et éventuellement des tests spécifiques supplémentaires)
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
	 * Il est possible de spécifier une section particulière dans ce fichier pour ne récupérer qu'une configuration particulière
	 * Cela permet de créer divers environnements LDAP (production, dev, ...)
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
			// s'il est précisé de lire une section (environnement) particulière
			if (!empty($section))
			{ 
				// on parse le fichier ini
				$data = parse_ini_file($filename, true); 
				// si la section demandée est disponible
				if (isset($data[$section]))
				{ 
					// on positionne les options
					$this->setOptions($data[$section]);
				}
			}
			else
			{ 
				// si aucune section n'est spécifiée, on parse le fichier ini en tenant compte des sections
				$data = parse_ini_file($filename, true); 
				// puis on récupère la première section trouvée
				$section = each($data); 
				// dont on positionne ensuite les options.
				// on spécifie $section[1] car each renvoie un tableau mixte vecteur/hashtable (cf doc PHP)
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
	 * Positionne les options de configuration passées en paramètre
	 * 	 Si la directive inifile est présente, on ne traite pas les autres (comportement exclusif)
	 * 
	 * @param array $data 
	 * @access public 
	 * @return void 
	 */
	public function setOptions($data = null)
	{
		// si les données de conf ne sont pas nulles
		if (is_array($data) && !empty($data))
		{
			// on teste si l'option inifile est présente => chargement conf via fichier ini
			if (isset($data['inifile']))
			{ 
				// on teste si on a précisé une conf particulière dans le fichier ini (i.e. section)
				$configSection = (isset($data['configSection'])) ? $data['configSection'] : false; 
				// on charge le fichier ini
				$this->loadIniFile($data['inifile'], $configSection);
			}
			else
			{ 
				// s'il n'y a pas d'option inifile, on charge les options présentes dans le tableau
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
