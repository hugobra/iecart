<?php
include 'bdd.class.php';

abstract class Objectif
{
        public static function get($id){
                if ($id == 1){
                        return "Connaitre l\'état de l\'installation";
                }else if($id == 2){
                        return "Conduire l\'installation";
                }else if($id == 3){
                        return "Travailler en équipe";
                }else if($id == 4){
                        return "Assurer la continuité du service";
                }
        }

        public static function getInverse($id){
                if ($id == "Connaitre l'état de l'installation"){
                        return 1;
                }else if($id == "Conduire l'installation"){
                        return 2;
                }else if($id == "Travailler en équipe"){
                        return 3;
                }else if($id == "Assurer la continuité du service"){
                        return 4;
                }
        }
}

$bdd = new BDD();
$objectifQuery = $bdd->_connexion->Query("SELECT * FROM objectifs");
$objectifQuery->setFetchMode(PDO::FETCH_OBJ);
$nbrObj = 1;
while ($objectifFetch = $objectifQuery->fetch()){
        echo "Objectif a modifier: " . $objectifFetch->idObjectif . "<br>";

        echo "Objectif: " . $objectifFetch->objectif . "<br>";

        $fapQuery = $bdd->_connexion->Query("SELECT * FROM fap WHERE idFAP = " . $objectifFetch->idFap);
        $fapQuery->setFetchMode(PDO::FETCH_OBJ);
        $fap = $fapQuery->fetch();

        echo "Fonction: " . $fap->fonction . "<br>";

        echo "SELECT * FROM objectifsstage WHERE idStage = " . $fap->idStage . " AND idFonction = " . $fap->fonction . " AND nomObjectif = '" . Objectif::get($objectifFetch->objectif) . "'<br>";

        $objQuery = $bdd->_connexion->Query("SELECT * FROM objectifsstage WHERE idStage = " . $fap->idStage . " AND idFonction = " . $fap->fonction . " AND nomObjectif = '" . Objectif::get($objectifFetch->objectif) . "'");
        $objQuery->setFetchMode(PDO::FETCH_OBJ);
        $obj = $objQuery->fetch();

        $bdd->_connexion->Query("UPDATE objectifs SET objectif = " . $obj->idObjectif . " WHERE idObjectif = " . $objectifFetch->idObjectif);

        //echo "SELECT * FROM objectifsstage WHERE idStage = " . $fap->idStage . " AND idFonction = " . $fap->fonction . " AND nomObjectif = " . Objectif::get($objectifFetch->objectif) . "<br>";

        echo "Objectif a mettre: " . $obj->idObjectif . "<br><br>";
}




echo "NO ERROR !";
?>
