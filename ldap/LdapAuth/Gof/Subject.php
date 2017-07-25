<?php
/**
 * Observer_FileLogger
 * 
 * @package LdapAuth
 **/

//include_once "lib/Gof/Event.php";

/**
 * Gof_Subject
 * 
 * @package LdapAuth
 * @author guennoc-nic
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public
 **/
class Gof_Subject{
	/**
	 * Constructor
	 * @access protected
	 */
	protected function __construct()
	{
	}
	
	/**
	 * 
	 * @access private
	 * @var string
	 **/
	private $_observers = array();
	
	/**
	 * Ajoute un observateur au sujet
	 * @access public
	 * @param string $name Le nom de l'observateur à ajouter
	 * @param object $observer L'instance de l'observateur à ajouter
	 * @return 
	 **/
	public function attach($name, &$observer)
	{
		$observer->setSubject($this);
		$this->_observers[$name] =& $observer;
	}
	
	/**
	 * Permet de retirer l'observateur spécifié
	 * @access public
	 * @param string $name Le nom de l'observateur à retirer
	 * @return void
	 **/
	public function detach($name)
	{
		unset($this->_observers[$name]);
	}
	
	/**
	 * Notification d'un évènement aux observateurs enregistrés
	 * @access public
	 * @param string $event message
	 * @param string $class Classe source de l'erreur
	 * @param string $function Fonction source de l'erreur
	 * @param integer $line Ligne source de l'erreur
	 * @return void
	 **/
	public function notify($event = null, $class = __CLASS__, $function =  __FUNCTION__, $line = __LINE__)
	{
		if (is_array($this->_observers) && !empty($this->_observers)) {
			$names = array_keys($this->_observers);
			foreach($names as $name){
				$observer =& $this->_observers[$name];
				$observer->update($event, $class, $function, $line);
			}    
		}
	}
}

?>