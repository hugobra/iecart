<?php
/**
 * Gof_Event
 * 
 * @package LdapAuth
 */

/**
 * Gof_Observer
 * 
 * @package LdapAuth
 * @author guennoc-nic 
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @abstract 
 * @access public 
 */
abstract class Gof_Observer
{
	/**
	 * 
	 * @access private 
	 * @var string 
	 */
	protected $_options = array();

	/**
	 * 
	 * @access private 
	 * @var Object 
	 */
	private $_subject = null;

	/**
	 * 
	 * @access public 
	 * @return Object 
	 */
	public function &getSubject()
	{
		return $this->_subject;
	}

	/**
	 * 
	 * @access public 
	 * @return void 
	 */
	public function setSubject(&$newValue)
	{
		$this->_subject = &$newValue;
	}

	/**
	 * Constructor
	 * 
	 * @access protected 
	 */
	protected function __construct()
	{
	}

	/**
	 * 
	 * @access public 
	 * @return void 
	 */
	abstract public function update($event, $class, $function, $line);
	
	
	
	// GETTER / SETTER
	/**
	 * Retourne les options de l'observateur
	 * 
	 * @access public 
	 * @return array les options de l'observateur
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Positionne les options de l'observateur
	 * 
	 * @param array $newValue les options de l'observateur
	 * @access public 
	 * @return void 
	 */
	public function setOptions($newValue)
	{
		$this->_options = $newValue;
	}
} 

?>