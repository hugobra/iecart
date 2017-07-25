<?php
include_once '../config.php';
include_once '../classes/bdd.class.php';
include_once '../classes/missions.class.php';
include_once '../classes/site.class.php';
include_once '../classes/user.class.php';
include_once '../classes/ecart.class.php';
include_once '../classes/class.phpmailer.php';
include_once '../classes/class.smtp.php';
/*
Démarrage de session, récupération de la date du jour
*/

session_cache_limiter('private, must-revalidate');
session_start();
$date = date("Y-m-d");

/*
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output

$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = 'mailhost.der.edf.fr';
// use
//$mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 25;
//Set the encryption system to use - ssl (deprecated) or tls
//$mail->SMTPSecure = 'ssl';
//Whether to use SMTP authentication
//$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
//$mail->Username = "hugo.branly@edf.fr";
//Password to use for SMTP authentication
//$mail->Password = "6XD2fpdp+";
//Set who the message is to be sent from
$mail->setFrom('hugo.branly@edf.fr', 'Fiche écart');
//Set an alternative reply-to address
$mail->addReplyTo('hugo.branly@edf.fr', 'Fiche écart');
//Set who the message is to be sent to
$mail->addAddress('hugo.branly@edf.fr', 'HB');
//Set the subject line
$mail->Subject = 'PHPMailer GMail SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->Body = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
/*if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}*/

/*
$mail_to = "hugo.branly@edf.fr"; //Destinataire
$from_mail = "hugo.branly@edf.fr"; //Expediteur
$from_name = "iEcarts"; //Votre nom, ou nom du site
$reply_to = "hugo.branly@edf.fr"; //Adresse de réponse
$subject = "Objet du mail";    
$file_name = "piece_jointe.pdf";
$path = $_SERVER['DOCUMENT_ROOT']."/fichiers";
$typepiecejointe = filetype($path.$file_name);
$data = chunk_split( base64_encode(file_get_contents($path.$file_name)) );
//Génération du séparateur
$boundary = md5(uniqid(time()));
$entete = "From: $from_mail \n";
$entete .= "Reply-to: $from_mail \n";
$entete .= "X-Priority: 1 \n";
$entete .= "MIME-Version: 1.0 \n";
$entete .= "Content-Type: multipart/mixed; boundary=\"$boundary\" \n";
$entete .= " \n";
$message  = "--$boundary \n";
$message .= "Content-Type: text/html; charset=\"iso-8859-1\" \n";
$message .= "Content-Transfer-Encoding:8bit \n";
$message .= "\n";
$message .= "Bonjour,<br />Veuillez trouver ci-joint le bon de commande<br/>Cordialement";
$message .= "\n";
$message .= "--$boundary \n";
$message .= "Content-Type: $typepiecejointe; name=\"$file_name\" \n";
$message .= "Content-Transfer-Encoding: base64 \n";
$message .= "Content-Disposition: attachment; filename=\"$file_name\" \n";
$message .= "\n";
$message .= $data."\n";
$message .= "\n";
$message .= "--".$boundary."--";

mail($mail_to, $subject, $message, $entete);

$subject="Test mail";
$to="hugo.branly@edf.fr";
$body="This is a test mail";
if (mail($to,$subject,$body)){
echo "Mail sent successfully!";
}else{
echo"Mail not sent!";}
*/

/* Récupération du nouvel id de stage */
$idMissionActuel = Mission::getNewMissionNumber();

$site = new Site($_SESSION['site']);
$users=UserManager::getAllAgentsFromSite($site->_id);
$typeécarts= TypeEcart::getAllFromSite($site->_id);

if (isset($_POST['titre'], $_POST['description'],$_POST['responsable'])){
	$mission= Mission::creer($idMissionActuel,$_POST['titre'], $_POST['description'], $_POST['responsable'],$site);
		
	
	header("Location: ficheMission.php?id=" . $idMissionActuel);
}




/*Inclusion des pages html si la variable de session d'utilisateur est bien initialisée*/
if(isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
	include("../tpl/pages/creer_mission.tpl.html");
else
	include("connexion.php");

?>
