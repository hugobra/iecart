<?php
/**
 * Gof_Event
 * 
 * @package LdapAuth
 **/

/**
 * Gof_Event
 * 
 * @package LdapAuth
 * @author guennoc-nic
 * @copyright Copyright (c) 2005
 * @version $Id$
 * @access public
 **/
class Gof_Event{
	/**
	 * Constructor
	 * @access protected
	 */
	protected function __construct($options = null){
		$this->setOptions($options);
	}
	
	/**
	 * 
	 * @access public
	 * @return void 
	 **/
	public function setOptions($options = null)
	{
		if (!is_array($options)) {
			return true;
		}
		
		foreach($options as $field => $value){
			switch($field){
				case 'type': 
					$this->setType($value);
					break;
				case 'message': 
					$this->setMessage($value);
					break;
				case 'object': 
					$this->setObject($value);
					break;
				default:
					break;
			} // switch
		}
	}
	
	/**
	 *
	 * @access public
	 * @return void 
	 **/
	public function getArguments($args = null)
	{
		if (!is_array($args)) {
		    return array();
		}
		
		if (count($args) != 1 && count($args) >= 3) {
			$options = array(
				'type' => array_shift($args),
				'object' => array_shift($args),
				'message' => array_shift($args)
			);
			
			return $options;
		} elseif (count($args) == 1 && count($args[0]) >= 3) {
			return $options;
		} else {
			return array();
		}
		return array();
	}
	
	/**
	 * Design Pattern : singleton
	 * 
	 * @access public 
	 * @return object $instance la référence sur l'instance
	 */
	public function &getInstance($options = null)
	{
		static $t_instance = array();
		
		$options = Gof_Event::getArguments(func_get_args());

		$id = md5(serialize($options));
		
		if (!isset($t_instance[$id]))
		{
			$t_instance[$id] =& new Gof_Event($options);
		}
		return $t_instance[$id];
	}
	
	/**
	 * 
	 * @access private
	 * @var string
	 **/
	private $_type = '';
	
	/**
	 * 
	 * @access public
	 * @return string 
	 **/
	public function getType(){
		return $this->_type;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 **/
	public function setType($newValue){
		$this->_type = $newValue;
	}
	
	/**
	 * 
	 * @access private
	 * @var string
	 **/
	private $_message = '';
	
	/**
	 * 
	 * @access public
	 * @return string 
	 **/
	public function getMessage(){
		return $this->_message;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 **/
	public function setMessage($newValue){
		$this->_message = $newValue;
	}

	/**
	 * 
	 * @access private
	 * @var string
	 **/
	private $_object = '';
	
	/**
	 * 
	 * @access public
	 * @return string 
	 **/
	public function getObject(){
		return $this->_object;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 **/
	public function setObject($newValue){
		$this->_object = $newValue;
	}

	/**
	 *
	 * @access public
	 * @return void 
	 **/
	public function toArray()
	{
		return array(
			'type' => $this->getType(),
			'object' => $this->getObject(),
			'message' => $this->getMessage()
		);
	}
}


?>