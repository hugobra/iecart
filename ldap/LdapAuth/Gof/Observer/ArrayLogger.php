<?php
/**
 * Gof_Event
 * 
 * @package LdapAuth
 */

/**
 */
include_once(dirname(__FILE__) . "/../Observer.php");

/**
 * Observer_ArrayLogger
 * 
 * @package LdapAuth
 * @author guennoc-nic 
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public 
 */
class Observer_ArrayLogger extends Gof_Observer
{
	/**
	 * Constructor
	 * 
	 * @access public 
	 */
	public function __construct($options = null)
	{
		if (!is_null($options))
		{
			$this->setOptions($options);
		}
    }

	/**
	 * 
	 * @access private 
	 * @var string 
	 */
	private $_log = '';

	/**
	 * 
	 * @access public 
	 * @return string 
	 */
	public function getLog()
	{
		return $this->_log;
    }

	/**
	 * 
	 * @access public 
	 * @return void 
	 */
	public function setLog($newValue)
	{
		$this->_log = $newValue;
	}

	/**
	 * Ecrit dans un tableau PHP les messages d'erreur générés
	 * 
	 * @access public 
	 * @param string $event message
	 * @param string $class Classe source de l'erreur
	 * @param string $function Fonction source de l'erreur
	 * @param integer $line Ligne source de l'erreur
	 * @return void
	 */
	public function log($event = null, $class = null, $function = null, $line = null)
	{
		$time = date("d/m/y H:i:s |");
		$this->_log[] = "$time $class::$function():$line => $event";
	}

	/**
	 * Mise à jour de l'observateur
	 * 
	 * @access public 
	 * @param string $event message
	 * @param string $class Classe source de l'erreur
	 * @param string $function Fonction source de l'erreur
	 * @param integer $line Ligne source de l'erreur
	 * @return void
	 */
	public function update($event, $class, $function, $line)
	{
		$this->log($event, $class, $function, $line);
	}
}

?>