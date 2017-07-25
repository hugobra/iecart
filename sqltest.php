<?php
$connexion = new PDO('mysql:host=localhost;dbname=testsql', 'root', '');

$queryFap = $connexion->query("SELECT * FROM fap");
while ($fetchFap = $queryFap->fetch()){
	
	echo "<hr>";
}
?>