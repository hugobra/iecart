<?php

/*Initialisation de l'application*/

try{
  if(empty($_SESSION["connexion"]))
    include("connexion.php");
  else
    include("stagiaires.php");
      
    }

catch(Exception $e) {include("404.php");}

?>
