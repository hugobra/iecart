<style>
	page{
		background: url(./res/filigrane.jpg);
	}
	table {
		border-collapse: collapse;
	}
	table, tr, td{
		border: solid 1px black;
	}
	td.no-col{
		border: solid 0px black;
	}
	.info{
		font-size: 13px;
	}
	.objectif{
		width: 25.52%;
		font-weight: bold;
		padding-top: 20px;
		padding-bottom: 20px;
		padding-left: 20px;
		padding-right: 20px;
	}
	.point{
		width:37.5%;
		font-size: 11px;
		text-align: left;
		padding-left: 5px;
		padding-right: 5px;
		text-align: left;
	}
</style>
<?php
foreach ($_GET['idFaps'] as $idfap) {
	$fap = new FAP($idfap);


	// verifier que le site a bien le droit d'afficher toutes les faps
	$sites = $fap->_stagiaire->getAllSite();
	// test du site

	$goodSite = false;
	if ($site->_id == $fap->_stagiaire->_site->_id){
		$goodSite = true;
	}
	foreach ($sites as $siteTest) {
		if ($siteTest->_id == $site->_id){
			$goodSite = true;
		}
	}
	if(!$goodSite){
		exit("Ce site n'a pas les droits d'afficher une ou plusieurs de ces faps <a href='/route/fap.php'>Retour liste faps</a>");
	}
?>

<page style="font-size: 10pt; font-family:Arial;" <!--backimg="./res/filigrane.jpg" --> backtop="3mm" backbottom="3mm" backleft="-7mm" backright="-7mm">

	<table style="width:100%;text-align: center;">
		<tr>
			<td style="width:15%;"><img src="./res/logo_edf.png" alt="logo" style="width: 15mm"></td>
			<td style="width:65%;"><h1 style="font-size:26px">FICHE D'AIDE A LA PROGRESSION</h1></td>
			<td style="width:20%;"><strong style="font-size:16px">EDF - DPNT </strong><br/><br/> <strong style="font-size:16px">UFPI</strong></td>
		</tr>
		<tr>
			<td colspan="3" align="left">
				<span style="margin-left:8px; margin-top:4px;"><strong>IDENTIFICATION :</strong></span>
				<span class="info" style="margin-left:8px; margin-top:4px;">Nom et prénom du stagiaire : <strong> <?=$fap->_stagiaire->_nom . " " . $fap->_stagiaire->_prenom?></strong></span>
				<div class="info" style="position: absolute; left: 630px; top: 100px;"> Division : DPNT </div>
				<br><br>
				<span class="info" style="margin-left:135px; margin-top:4px;">Fonction : <?=$fap->_fonction->_nom?></span>
				<span class="info" style="margin-left:135px; margin-top:4px; left: 320px;">NNI : <?=$fap->_stagiaire->_nni?></span>
				<br><br>
				<span class="info" style="margin-left:135px; margin-top:4px;">Unité : CNPE de <?=$fap->_stagiaire->_site->_nom?></span>
				<div class="info" style="position: absolute; left: 320px; top: 157px;">Service : <?=$fap->_stagiaire->_service->_nom?></div>
				<div class="info" style="position: absolute; left: 520px; top: 157px;">DUA : <?=$fap->_stagiaire->_dua?> </div>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left">
				<span style="margin-left:8px; margin-top:4px;"><strong>ACTION :</strong></span>
				<span class="info" style="margin-left:63px; margin-top:4px;">Code Action: <?=$fap->_codeAction?></span>
				<div class="info" style="position: absolute; left: 320px; top: 178px;">Intitulé : <strong><?=$fap->_stage->_titre?></strong></div>
				<br><br>
				<span class="info" style="margin-left:135px; margin-top:4px;">Code Session : <?=$fap->_codeSession?></span>
				<div class="info" style="position: absolute; left: 340px; top: 207px;">Du <?=$fap->_dateDebut?></div>
				<div class="info" style="position: absolute; left: 540px; top: 207px;">Au <?=$fap->_dateFin?></div>
				<br><br>
				
				<span style="margin-left:8px; margin-top:4px; font-size: 14px;">Absence :</span>
				<span class="info" style="margin-left:63px; margin-top:4px;">Du 
				<?php if($fap->_nbabsence!="0"){ ?> <?=$fap->_absenceD?> <?php }else{?> XX/XX/XXXX <?php } ?> </span>
				<div class="info" style="position: absolute; left: 300px; top: 238px;">au 
				<?php if($fap->_nbabsence!="0"){ ?> <?=$fap->_absenceF?> <?php }else{?> XX/XX/XXXX <?php } ?></div>
				<div class="info" style="position: absolute; left: 540px; top: 238px;">Nb de 1/2 journée : <?=$fap->_nbabsence?></div>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left">
				<span class="info" style="font-size: 16px;margin-left:8px; margin-top:4px;">Avertissement à l’attention des stagiaires et de leur hiérarchie :</span>
				<br>
				<div style="font-size: 11px;margin-left:8px;margin-top:4px;margin-bottom: 4px;">Cette fiche permet d’attester des points forts et des points à améliorer d’un stagiaire, suite à l’action de formation réalisée, et en
aucun cas d’attester de ses compétences (à évaluer en situation réelle de travail). Les managers sont responsables du
développement des compétences de leurs agents. Il appartient à la hiérarchie du stagiaire de décider les mesures d’accompagnement
éventuelles à mettre en œuvre pour traiter les points à améliorer</div>
			</td>
		</tr>
	</table>

	<table style="width:100%;text-align: center;">
		<tr>
            <td rowspan="2" style="width: 25.52%; text-align: center; border: solid 1px black; padding-bottom:8px;"><strong style="font-size:10pt; margin-top:4px; margin-bottom:1px;">OBJECTIFS DE FORMATION CONCERNES (OF)</strong></td>
            <td colspan="2" style="width: 70%; text-align: center; border: solid 1px black; background-color:#C0C0C0; font-size:7pt;"> OBSERVATIONS REALISEES </td>
        </tr>
        <tr>
            <td style="width:37.5%; text-align: center; border: solid 1px black; background-color:#C0C0C0; border-left:none;">Points forts</td>
		    <td style="width:37.5%; text-align: center; border: solid 1px black; background-color:#C0C0C0;">Points à améliorer</td>
        </tr>
        <?php foreach ($fap->_objectifs as $objectif) { ?>
        <tr>
        	<td class="objectif"><?=trim($objectif->_nom)?></td>
			<?php //Afin d'avoir un rendu identique à l'attendu 
							$àremplacer=array("\n","Ã ", "Ã¨","Ã©"," < "," > ");
							$remplacant=array("<br/>","&agrave;","&egrave;","&eacute;"," &lt; "," &gt; ");
							$pf = utf8_decode(str_replace($àremplacer,$remplacant,utf8_encode($objectif->_results['PF'])));
							$pa = utf8_decode(str_replace($àremplacer,$remplacant,utf8_encode($objectif->_results['PA'])));
							$pa=preg_replace("#(Ecart.{0,6}:)#","<b><u>$0</u></b>",$pa);
							$pa=preg_replace("#(Exemple.{0,6}:)#","<b><u>$0</u></b>",$pa);
							$pa=preg_replace("#(Conseil.{0,6}:)#","<b><u>$0</u></b>",$pa);
							?>
        	<td class="point"><?=trim($pf)?></td>
			<td class="point"><?=trim($pa)?></td>
        </tr>
        <?php } ?>
        <tr>
        	<td colspan="3" align="left">
			<?php //Afin d'avoir un rendu identique à l'attendu 
							$àremplacer=array("\n","Ã ", "Ã¨","Ã©"," < "," > ");
							$remplacant=array("<br/>","&agrave;","&egrave;","&eacute;"," &lt; "," &gt; ");
							$comm = utf8_decode(str_replace($àremplacer,$remplacant,utf8_encode($fap->_commentaires)));
							
							?>
        		<span style="margin-left:8px; margin-top:4px;margin-bottom:8px;"><strong>Observations du stagiaire :</strong> <?=trim($comm)?></span>
        	</td>
        </tr>
	</table>
	<table style="width:100%;text-align: left;">
	<?php
	$dateModif = new DateTime($fap->_dateModif);
	$dateModif = $dateModif->format('d/m/Y');
	?>
		<tr>
			<td style="width: 25.13%;">
				<span style="margin-left:8px; margin-top:4px;"><strong>STAGIAIRE : </strong></span>
				<br>
				<br>
				<br>
				<br>
				<span style="margin-left:8px; margin-top:4px;">Visa : </span>
			</td>
			<td style="width: 25.13%;">
				<span style="margin-left:8px; margin-top:2px;">Date : <?=date("d/m/Y")?></span>
				<br>
				<br>
				<br>
				<br>
				<br>
			</td>
			<td style="width: 25.13%;">
				<span style="margin-left:8px; margin-top:4px;"><strong>FORMATEUR : </strong></span>
				<br>
				<br>
				<span style="margin-left:8px; margin-top:4px;">Nom : <?=$fap->_formateur?></span>
				<br>
				<br>
				<span style="margin-left:8px; margin-top:4px;">Visa : </span>
			</td>
			<td style="width: 25.13%;">
				<span style="margin-left:8px; margin-top:2px;">Date : <?=date("d/m/Y")?></span>
				<br>
				<br>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<span style="margin-left:8px; margin-top:4px;"><strong>DIFFUSION : </strong>Original : <span style="font-size: 12px;">agent</span></span>
				<span style="margin-left:130px; margin-top:4px;">Copies : <span style="font-size: 12px;">Gestionnaire du site de formation (Dossier suivi de session)</span></span>
				<br>
				<span style="margin-top:8px; margin-bottom:8px; margin-left: 30px; font-size: 12px;"><strong>Cette fiche peut être diffusée à la hiérarchie de l’agent si le cahier des charges de formation le spécifie clairement.</strong></span>
			</td>
		</tr>
	</table>

<!-- 	<table style="width:100%;background: red;">
		<tr>
			<td>Connaître l'état de l'installation</td>
			<td style="width:35%;">La prise d'informations lors de la prise de
quart est pertinente, l'état de tranche est
connu ainsi que la justification des
différentes alarmes en SdC.</td>
			<td style="width:35%;">Pas de remarques particulières.</td>
		</tr>
	</table>  -->
</page>
<page_footer>
<span style="font-size: 9px;">Copyright EDF - <?=date("Y")?>. Ce document est la propriété d'EDF. Toute communication, reproduction, publication, même partielle, est interdite sauf autorisation.
<br>Edité avec iFAP™</span>
</page_footer>
<?php
}
?>
