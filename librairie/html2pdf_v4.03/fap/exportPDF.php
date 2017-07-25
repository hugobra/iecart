<?php
$dontincludeconfig = true;

include_once '../../../config.php';
include_once '../../../classes/fap.class.php';


	session_start();
    ob_start();

if(!isset($_SESSION['nom2']) && $_SESSION['nom2']!=" ")
    header("Location: /route/");

$site = new Site($_SESSION['site']);

    try
{
    include(dirname(__FILE__).'/res/exportFAP.php');
    $content = ob_get_clean();


    // convert in PDF
    require_once(dirname(__FILE__).'/../html2pdf.class.php');
    
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(15, 5, 15, 5));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $date = date("Y-m-d");
	$html2pdf->Output('fap'.(string)$date.'.pdf');
    }

catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}
 
