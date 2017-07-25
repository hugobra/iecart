<?php
	session_start();
    ob_start();
    try
{
    $bdd = new PDO('mysql:host=10.122.1.39:3308;dbname=test', 'root', 'sUcyfSHWD#?a');
    include(dirname(__FILE__).'/res/exportFAP.php');
    $content = ob_get_clean();


    // convert in PDF
    require_once(dirname(__FILE__).'/../html2pdf.class.php');
    
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(15, 5, 15, 5));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('test.pdf');
    }

catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}
 
