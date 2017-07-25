<?php
if (!isset($dontincludeconfig)) {
	include_once '../config.php';
}

/**
* Classe de gestion de la base de donnée
*/
class BDD
{
	public $_connexion;
	
	/**
	* Constructeur de la base de données, connecte automatiquement et renvoie la connexion
	*/
	function __construct(){
		try{
			$this->_connexion = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
	}

	/**
	* Fonction de fermeture de la connexion à la base de donnée
	*/
	public function Close(){
		$this->_connexion = null;
	}

	public static function Quote($string){
		$bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		return $bdd->quote($string);
	}
}
?>