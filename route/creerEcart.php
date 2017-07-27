<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';
include_once '../../smtpclass/class.phpmailer.php';
include_once '../../smtpclass/class.smtp.php';

/*
Démarrage de session, récupération de la date du jour
*/
session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

$site = new Site($_SESSION['site']);

if(isset($_GET['id'])){
	$user = new UserManager($_GET['id']);

	$missions = Mission::getAllFromSite($site);

	$idEcart = Ecart::getNewEcartNumber();
	

	if(!empty($_POST['missionselect']) && !empty($_POST['titre']) && !empty($_POST['description']) && !empty($_POST['propotraitement'])){
		
		$mission= new Mission($_POST['missionselect']);
		$recepteur=$mission->_responsable->_id;
		//echo '<script>alert("'.$recepteur.'");</script>' ;
		Ecart::create($idEcart, $user->_id, $mission->_id, $recepteur,  $_POST['titre'], $_POST['description'], $_POST['propotraitement'], $date, $site );
		
		// Les valeurs restent pendant 30 minutes
		/*setcookie("stage", $_POST['stage'], time() + 3600 / 2);
		setcookie("codeAction", $_POST['codeAction'], time() + 3600 / 2);
		setcookie("codeSession", $_POST['codeSession'], time() + 3600 / 2);
		setcookie("dateDebut", $_POST['dateDebut'], time() + 3600 / 2);
		setcookie("dateFin", $_POST['dateFin'], time() + 3600 / 2);*/

		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 2;
		//Ask for HTML-friendly debug output

		$mail->Debugoutput = 'html';
		
		// use
		//$mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 25;
		//Set the encryption system to use - ssl (deprecated) or tls
		//$mail->SMTPSecure = 'ssl';
		//Whether to use SMTP authentication
		//$mail->SMTPAuth = true;
	
		//Set who the message is to be sent from
		$mail->setFrom($user->_mail, 'Fiche écart');
		//Set an alternative reply-to address
		$mail->addReplyTo($user->_mail, 'Fiche écart');
		//Set who the message is to be sent to
		$mail->addAddress($mission->_responsable->_mail, "Responsable traitement de l'écart");
		//Set the subject line
		$mail->Subject = "Vous avez reçu une nouvelle fiche d'écart";
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		//Replace the plain text body with one created manually
		$mail->Body = "Pour accéder au traitement de vos écarts, connectez-vous à la page suivante: http://10.165.116.142/route/";
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
			}
		header("Location: /route/ecarts.php");
	} 
	 
	/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
	if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
		include("../tpl/pages/creer_ecart.tpl.html");
	else
		include("connexion.php");
}


?>
