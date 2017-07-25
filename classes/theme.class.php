<?php
/**
* Classe concernant les thèmes de stage
*/
class Theme
{
	public $_id;
	public $_stage;
	public $_nom;

	function __construct($id, $stage = null, $nom = null){
		$this->_id = $id;

		if (empty($idStage) && empty($nom)){
			$this->getInfos();
		}else{
			$this->_stage = $stage;
			$this->_nom = $nom;
		}
	}

	/**
	* Permet d'obtenir les informations concernant un theme
	*/
	public function getInfos(){
		$bdd = new BDD();

		$id = $bdd->_connexion->Quote($this->_id);

		$themeQuery = $bdd->_connexion->Query("SELECT * FROM themes WHERE idTheme = " . $id);
		if (!$themeQuery){
			return false;
		}
		$themeQuery->setFetchMode(PDO::FETCH_OBJ);
		$themeFetch = $themeQuery->fetch();

		$this->_nom = $themeFetch->theme;
		$this->_stage = new Stage($themeFetch->idStage);

		return true;
	}
}
?>