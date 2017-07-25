<?php
$content = 'A Test overflow<br>A Test overflow<br>A Test overflow<br>
<img src="./res/logo.gif" alt="logo" style="width: XXXmm"><br>
B Test overflow<br>B Test overflow<br>B Test overflow<br>
<img src="./res/logo.gif" alt="logo" style="width: XXXmm"><br>
C Test overflow<br>C Test overflow<br>C Test overflow<br>';
?>
<style type="text/css">
<!--
div.zone
{
    border: solid 2mm #66AACC;
    border-radius: 3mm;
    padding: 1mm;
    background-color: #FFEEEE;
    color: #440000;
}
div.zone_over
{
    width: 30mm;
    height: 35mm;
    overflow: hidden;
}

-->
</style>
<?php 

$sites=$bdd->query('SELECT * FROM site WHERE idSite='.$_SESSION['site']);
$site=$sites->fetch();
$date=date("d-m-Y");
	foreach($_POST['idFaps'] as $id){		
		$faps=$bdd->query('SELECT DISTINCT fap.*, stagiaire.nom, stagiaire.prenom, stage.titre, stagiaire.service, stagiaire.fonctionActuelle FROM fap,stagiaire,stage WHERE stagiaire.idStagiaire=fap.idStagiaire AND stage.idStage=fap.idStage AND idFAP='.$id);
		$objectifs=$bdd->query('SELECT * FROM objectifs WHERE idFap='.$id);
		$fonctions=$bdd->query('SELECT DISTINCT fonction.* FROM fonction,fap WHERE fap.idStagiaire=fonction.idStagiaire AND idFap='.$id);
		$fonction=$fonctions->fetch();
		$fap=$faps->fetch();
		?>

<page style="font-size: 10pt; font-family:Arial" backtop="3mm" backbottom="3mm" backleft="-7mm" backright="-7mm">
    <table style="width: 100%; border: double 3px black; border-collapse: collapse; bottom-margin:4px;" align="center">
        <tr>
            <td style="width: 15%; text-align: center; border: solid 1px black;"><img src="./res/logo_edf.png" alt="logo" style="width: 15mm"></td>
            <td style="width: 65%; text-align: center; border: solid 1px black;"><h1 style="font-size:26px">FICHE D'AIDE A LA PROGRESSION</h1></td>
            <td style="width: 20%; text-align: center; border: solid 1px black; border-left:none;"><strong style="font-size:16px">EDF - DPI </strong><br/><br/> <strong style="font-size:16px">UFPI</strong></td>
        </tr>
        <tr>
            <td colspan="3" style="width: 100%; border: solid 1px black; padding-bottom:8px;">
				<span style="margin-left:8px; margin-top:4px;"><strong>IDENTIFICATION : </strong></span>
				 <span style="margin-left:8px; margin-top:4px;">Nom et prénom du stagiaire : <?php echo $fap['nom']." ".$fap['prenom']; ?></span>  
				 <div style="position:absolute; left:640px; top:100px;">Division : DPN </div><br/>
                  <span style="margin-left:138px; margin-top:16px;">Fonction : <?php echo $fap['fonctionActuelle']; ?></span><br/>
                  <span style="margin-left:138px; margin-top:28px;">Unité : <?php echo $site['nomSite']; ?></span>
                  <div style="position:absolute; left:320px; top:152px;">Service : <?php echo $fap['service']; ?></div>
                  <div style="position:absolute; left:520px; top:152px;">DUA :</div>
		    </td>
		    </tr>
		    <tr>
            <td colspan="3" style="width: 100%; border: solid 1px black; padding-bottom:8px;">
				<span style="margin-left:8px; margin-top:4px;"><strong>ACTION : </strong></span>
				 <span style="margin-left:63px; margin-top:4px;">Code action : <?php echo $fap['codeAction']; ?></span>  
                  <div style="position:absolute; left:320px; top:181px;">Intitulé : <?php echo $fap['titre']; ?></div><br/>
                  <span style="margin-left:138px; margin-top:16px;">Code session : <?php echo $fap['codeSession']; ?></span>
                  <div style="position:absolute; left:340px; top:208px;">Du <?php echo $fap['dateDebut']; ?></div>
                  <div style="position:absolute; left:541px; top:208px;">Au <?php echo $fap['dateFin']; ?></div>
                  
                  <br/>
                  <span style="margin-left:8px; margin-top:28px; font-size:12pt;">Absence : </span>
                  <span style="margin-left:52px; margin-top:28px;">Du </span>
                  <div style="position:absolute; left:300px; top:238px;">au</div>
                  <div style="position:absolute; left:541px; top:238px;">Nb de 1/2 journée : 0 </div>
		    </td>
		               
        </tr>
        		    <tr>
		      <td colspan="3" style="width: 100%; border: solid 1px black; padding-bottom:8px; padding-left:8px; padding-right:8px;">
	<span style="font-size:11pt; margin-top:8px;">Avertissement à l’attention des stagiaires et de leur hiérarchie :<br/></span>
	<span style="font-size:9pt;">
Cette fiche permet d’attester des points forts et des points à améliorer d’un stagiaire, suite à l’action de formation réalisée, et en
aucun cas d’attester de ses compétences (à évaluer en situation réelle de travail).
Les managers sont responsables du développement des compétences de leurs agents. Il appartient à la hiérarchie du stagiaire de
décider les mesures d’accompagnement éventuelles à mettre en œuvre pour traiter les points à améliorer.</span>
		    </td>
		               
        </tr>
    </table>
    
    
     <table style="width: 100%; border: double 3px black; border-collapse: collapse;" align="center">
        <tr>
            <td rowspan="2" style="width: 30%; text-align: center; border: solid 1px black; padding-bottom:8px;"><strong style="font-size:11pt; margin-top:4px; margin-bottom:4px;">OBJECTIFS PEDAGOGIQUES GENERAUX CONCERNES</strong></td>
            <td colspan="2" style="width:70%; text-align: center; border: solid 1px black; background-color:#C0C0C0; font-size:7pt;"> (COCHER LA CASE CHOISIE ET REDIGER LES COMMENTAIRES SUR LE POINT EN QUESTION)</td>
		
        </tr>
        <tr>
            <td style="width:35%; text-align: center; border: solid 1px black; background-color:#C0C0C0;">Point fort</td>
		    <td style="width:35%; text-align: center; border: solid 1px black; background-color:#C0C0C0;">Point à améliorer</td>
		
        </tr>        
        <?php $objectif=$objectifs->fetch(); ?>
        <tr>
			<td style="text-align: center; border: solid 1px black; border-top:none;"><?php echo $objectif['objectif']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointFort']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointAAmeliorer']; ?></td>
        
        </tr>
        <?php $objectif=$objectifs->fetch(); ?>
        <tr>
			<td style="text-align: center; border: solid 1px black; border-top:none;"><?php echo $objectif['objectif']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointFort']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointAAmeliorer']; ?></td>
        
        </tr>
        <?php $objectif=$objectifs->fetch(); ?>
        <tr>
			<td style="text-align: center; border: solid 1px black; border-top:none;"><?php echo $objectif['objectif']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointFort']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointAAmeliorer']; ?></td>
        
        
        </tr>
        <?php $objectif=$objectifs->fetch(); ?>
        <tr>
			<td style="text-align: center; border: solid 1px black; border-top:none;"><?php echo $objectif['objectif']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointFort']; ?></td>
			<td style="width:35%; height:10%; text-align: left; border: solid 1px black;"><?php echo $objectif['pointAAmeliorer']; ?></td>
        
        
        </tr>
        
        
               <tr>
			<td colspan="3" style="width: 100%; border: solid 1px black; height:48px; vertical-align:none;">
			<span style="margin-left:8px; margin-top:4px;"><strong>OBSERVATIONS DU STAGIAIRE : </strong></span>
			</td>
        </tr>
        
        </table>
    <table style="width: 100%; border: double 3px black; border-collapse: collapse;" align="center">
        <tr>
			<td style="width: 25%; border: solid 1px black; padding-bottom:8px;">
				<span style="margin-left:8px; margin-top:4px;"><strong>STAGIAIRE : </strong></span><br/>
				<span style="margin-left:8px; margin-top:30px;">Visa : </span>
			</td>
			<td style="width: 25%; border: solid 1px black; vertical-align:top;">
				<span style="margin-left:8px; margin-top:4px;">Date : <?php echo $date; ?></span>

			</td>
			<td style="width: 25%; border: solid 1px black; padding-bottom:8px;">
				<span style="margin-left:8px; margin-top:4px;"><strong>FORMATEUR : </strong></span><br/>
				<span style="margin-left:8px; margin-top:12px;">Nom : <?php echo $_SESSION['nom2']; ?> </span> <br/>
				<span style="margin-left:8px; margin-top:20px;">Visa : </span>
			</td>
						<td style="width: 25%; border: solid 1px black; vertical-align:top;">
				<span style="margin-left:8px; margin-top:4px;">Date : <?php echo $date; ?></span>

			</td>
		</tr>
		<tr>
					<td colspan="4" style="width: 100%; border: solid 1px black; padding-bottom:4px;">
			<span style="margin-left:8px; margin-top:4px;"><strong>DIFFUSION : </strong>Original : <span style="font-size:9pt;">agent</span></span>
			<span style="margin-left:108px; margin-top:4px;">Copies : <span style="font-size:9pt;">Gestionnaire du site de formation (Dossier suivi de session)</span></span>
			<br/>
			<span style="margin-left:30px; margin-top:8px; font-size:9pt;"><strong>Cette fiche peut être diffusée à la hiérarchie de l’agent si le cahier des charges de formation le spécifie clairement.</strong></span>
			</td>
		</tr>
		
		<tr>
					<td colspan="4" style="width: 100%; border: solid 1px black; padding-bottom:20px;">
			<span style="margin-left:8px; margin-top:8px; font-size:7pt;"><strong>FORMULAIRE FAP : </strong></span>
			</td>
		</tr>
     </table>
     
     
    
    <page_footer> 
    <div style="font-size : 10px; margin-left:-24px;">
    Copyright EDF - 2011. Ce document est la propriété d'EDF. Toute communication, reproduction, publication, même partielle, est interdite sauf autorisation. Edité avec iFAP™
    </div>
    </page_footer>
</page>
<?php } ?>
