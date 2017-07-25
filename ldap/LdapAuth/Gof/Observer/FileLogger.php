<?php
/**
 * Observer_FileLogger
 * 
 * @package LdapAuth
 **/


/** 
 * 
 *
 **/
include_once(dirname(__FILE__) . "/../Observer.php");


/**
 * Observer_FileLogger
 * 
 * @package LdapAuth
 * @author guennoc-nic
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public
 **/
class Observer_FileLogger extends Gof_Observer{

	/**
	 * 
	 * @access private
	 * @var string
	 **/
 //   private $_options = '';
	
	

	/**
     * Constructor
     * @access protected
     */
	public function __construct($options = null){
		$this->setOptions($options);
	}
	
	/**
	 *
	 * @access public
	 * @return void 
	 **/
	/*function setOptions($options = null){
		if (is_null($options) || !is_array($options) || (is_array($options) && empty($options)) ){
		    return false;
		}
		$this->_options = $options;
	}*/
	
	
	/**
	 * 
	 * @access private
	 * @var string
	 **/
    private $_log = '';
	
	/**
	 * 
	 * @access public
	 * @return string 
	 **/
    public function getLog(){
		return $this->_log;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 **/
	public function setLog($newValue){
		$this->_log = $newValue;
	}
	
	/**
     * Ecrit dans un fichier les messages d'erreur générés
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
		$filename = $this->_options['filename'];
		
		if (is_writable(dirname($filename))) {
			
			$time = date("d/m/y H:i:s | ");
			
			$location = "$class::$function():$line";
			//$len = 20 - strlen($location);
			//$location .= str_repeat(' ', $len);
			
			$location = str_pad($location, 40, " ", STR_PAD_RIGHT);   
			
			
			
		    $fd = fopen($filename, 'a');
			flock($fd, LOCK_EX+LOCK_NB);
			
			fwrite($fd, "$time $location\t$event\n");
			flock($fd, LOCK_UN);
			fclose($fd);
		}
		
		
		
		//$this->_log[] = "$class / $function:$line => $event";
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