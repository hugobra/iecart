<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Page Conversion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  </head>
  <body>
	  <h1>Page de conversion pour les mots de passes et téléchargement des sources</h1>
	  <a href="developpement.zip" target="_blank"><button>Télécharger</button></a>
    <form action="conversion.php" method="post">
		<label>Entrez un mot de passe : </label><input type="text" name="mdp"/><br/>
		<label>Conversion en hash : </label> <?php echo hash('ripemd160',$_POST['mdp']); ?>
    </form>
  </body>
</html>
