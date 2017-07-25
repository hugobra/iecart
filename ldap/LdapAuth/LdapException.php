<?php
/**
 * LdapAuth_Error
 * 
 * @package LdapAuth
 */


/**
 * LdapAuth_Error
 * 
 * @package LdapAuth
 * @author guennoc-nic
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public 
 */
class LdapException extends Exception
{
	private $_code = null;
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct($message = null, $code = null)
	{
		parent::__construct($message);
		$this->_code = $code;
	}

	/**
	 * Charge le fichier ini de messages
	 *
	 * @access private
	 * @return array Les messages
	 */
	private static function loadIniFile()
	{
		static $messages = null;

		if (is_null($messages))
		{
			$messages = parse_ini_file(LDAPAUTH_PATH . "/messages.ini");
		}
		return $messages;
	}

	/**
	 * Retourne une erreur en récupérant le libellé complet à partir du nom court fourni
	 * @access public 
	 * @param string $type le type de l'erreur (en correspondance avec le fichiers messages.ini)
	 * @return object l'objet représentant l'erreur demandée
	 */
	public static function raiseError($type = null)
	{
		$messages = LdapException::loadIniFile();
		$message = (isset($messages[$type])) ? $messages[$type] : $messages['default'];
		return new LdapException($message, $type);
	}
	// GETTER / SETTER

	/**
	 * Positionne le message de l'erreur
	 *
	 * @access public 
	 * @return void 
	 */
	public function setMessage($newValue)
	{
		$this->_message = $newValue;
	}

	/**
	 * 
	 * @access public
	 * @return void
	 **/
	public function setErrorCode($newValue){
		$this->_code = $newValue;
	}

	/**
	 * 
	 * @access public
	 * @return string Le code d'erreur
	 **/	
	public function getErrorCode() {
		return $this->_code;
	}
}

?>