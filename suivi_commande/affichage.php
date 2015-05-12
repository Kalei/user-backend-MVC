<div id="content">
	<div id="content-main3" style="position:relative; padding:10px; margin:0">
	<div class="action">
<?php



	
if($secu==TRUE && !empty($idcommande)) {
//••••••••••••••••••••••••••••••••••••••••••••••••••
// Affichage du statut de la commande
//••••••••••••••••••••••••••••••••••••••••••••••••••
	
	$sql_info_com = "SELECT statut_commande.libelle, statut_commande.id_statut, alerte, satisfaction FROM commande, statut_commande WHERE commande.id_statut = statut_commande.id_statut AND idcommande=".$idcommande;
	$res_info_com = mysql_query($sql_info_com);
	$don_info_com = mysql_fetch_assoc($res_info_com);
	extract($don_info_com);
	echo '<h3>Etat de votre commande : '.$libelle.'</h3>';
	if($alerte=1) $alertko = "<big>&times;</big> Vous avez demandé des informations sur votre commande, Top Fishing va vous répondre.";

//••••••••••••••••••••••••••••••••••••••••••••••••••
// Affichage des d'erreurs .....................
//••••••••••••••••••••••••••••••••••••••••••••••••••
	echo '<p class="ok">'.$alertok.'</p>';	
	echo '<p class="ko">'.$alertko.'</p>';	
	


//••••••••••••••••••••••••••••••••••••••••••••••••••	
// Affichage des infos d'expédition
//••••••••••••••••••••••••••••••••••••••••••••••••••
	
	if($id_statut == 51) {
		$sql_info_exp = "SELECT * FROM commande_expedition WHERE id_commande=".$idcommande;
		$res_info_exp = mysql_query($sql_info_exp) or die ();
		
		echo "<ul>";
		while ($don_info_exp = mysql_fetch_array($res_info_exp)) {	
			extract($don_info_exp);				
	
			// Type d'expédition
			if($type_livraison =="colissimo") {
				$img_livraison ='<img src="http://www.top-fishing.fr/www/images/commande/livraison_collisimo.png" alt="colissimo" style=" float:right; padding:10px" />';
				$suivi = 'http://www.colissimo.fr/portail_colissimo/suivre.do?language=fr_FR&parcelnumber';
			}
			else if($type_livraison =="chronopost") {
				$img_livraison ='<img src="http://www.top-fishing.fr/www/images/commande/livraison_chronopost.png" alt="Chronopost"  style=" float:right;padding:10px"" />';
				$suivi = 'http://www.chronopost.fr/expedier/inputLTNumbersNoJahia.do?listeNumeros';
			}
			else if($type_livraison =="lettremax")  {
				$img_livraison ='<img src="http://www.top-fishing.fr/www/images/commande/livraison_lettremax.png" alt="Lettre Max"  style=" float:right; padding:10px"" />';
				$suivi = 0;
			}
	
			echo '<li style="border-bottom:1px solid #CCC; height:60px; line-height:60px">Votre colis n°'.++$i.' a pour numéro : '.$numero_colis.' expédié le <strong>'.$date_expedition.'</strong>';
			if(!empty($suivi))  echo ' <a href="'.$suivi.'='.$numero_colis.'" style="border-radius:6px;padding:3px 6px; margin: 0 0 0 50px; color:white;background-color:#F60;text-decoration:none;">Suivre votre colis </a>';
			echo $img_livraison.'</li>';
		}
		echo "</ul>";
	}
	
//••••••••••••••••••••••••••••••••••••••••••••••••••
// Lignes de la commande
//••••••••••••••••••••••••••••••••••••••••••••••••••

?>
</div> 
<div class="action">
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
		<input type="hidden" name="modif-commande" value="1" />
	<h3>Votre panier</h3>
	
	<table id="caddie" cellpadding="8" style="margin:10px; width:820px">
		<tr>
			<th>Photo</th>
			<th>Modèle/Article</th>
			<th>R&eacute;f</th>
			<th>Disponibilit&eacute;</th>
			<th width="220">Actions</th>
			<th>Qte</th>
			<th>Prix U.</th>
			<th>Total</th>
		</tr>
<?php
	$sql_li_co = "
		SELECT
			lignecommande.*, libelle_etat_ligne_commande as libelle_etat, libelle_externe,
			article.photo1, article.nom as article, article.url_article,modele,disponibilite, article.prix_public
		FROM
			lignecommande, etatlignecommande, article
		WHERE
			idcommande = ".$idcommande.'
			AND article.reference = lignecommande.reference
			AND etatlignecommande.id_etat_ligne_commande = lignecommande.id_etat_ligne_commande' ;
	$res_li_co = mysql_query($sql_li_co) or die('Affichage de la page impossible nous consulter pour aide');
	while($don_li_co = mysql_fetch_array($res_li_co)) {
		extract($don_li_co);
		
		// début de la ligne
		echo '<tr>';
		
		//Photo
		echo '<td><a href="/images_produits/'.$photo1.'" target="_blank"><img src="/images_produits/p_'.$photo1.'"/></td>';			
		//Nom article
		echo '<td>'.$modele.'<br /><a href="/catalogue/article/'.$url_article.'.html">'.$article.'</a></td>';			
		// Référence
		echo '<td>'.$reference.'</td>';		
			
		// Affichage du statut		
		if ($id_etat_ligne_commande == 11 || $id_etat_ligne_commande ==12)  echo '<td colspan="2"><p class="dispo_1">'.$libelle_externe.'</p></td>';			
		else if ($id_etat_ligne_commande == 21 || $id_etat_ligne_commande ==22)  echo '<td colspan="2"><p class="dispo">'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 23) echo '<td colspan="2"><p class="dispo_3">'.$libelle_externe.'<br />
(délais initial non respecté par le fournisseur)</p></td>';
		else if ($id_etat_ligne_commande == 24) echo '<td><p class="dispo_4">'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 25) echo '<td><p class="dispo_0">'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 26) echo '<td colspan="2"><p>'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 27) echo '<td colspan="2"><p>'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 31) echo '<td colspan="2"><p>'.$libelle_externe.' (échangé contre '.$echange.')</p></td>';
		else if ($id_etat_ligne_commande == 41) echo '<td colspan="2"><p>'.$libelle_externe.'</p></td>';
		else if ($id_etat_ligne_commande == 51) echo '<td colspan="2"><p><big class="ok">&radic;</big>'.$libelle_externe.'</p></td>';
		else echo '<td colspan="2"><p>'.$libelle_externe.'</p></td>';
		
		// Si Rupture formulaire de mise à jour
		if ($id_etat_ligne_commande ==24 || $id_etat_ligne_commande ==25)  {
			echo '<td nowrap="nowrap">';
			if ($id_etat_ligne_commande ==24) echo '<input type="radio" name="maj'.$reference.'" value="attendre" checked="checked" />Attendre jusqu\'à ce que l\'article soit livrée<small> (1)</small> <br />';
			if(!empty($echange)) { 
				echo	'<input type="radio" name="maj'.$reference.'" value="echange" checked="checked" />Echange <small>(2)</small> :'; 
				$sql_i_echange = mysql_query("SELECT modele, prix_public, url_article FROM article WHERE reference =".$echange);
				$don_i_echange = mysql_fetch_array($sql_i_echange);
				echo '<a href="/catalogue/article/'.$don_i_echange['url_article'].'.html">'.$don_i_echange['modele'].' : '.$don_i_echange['prix_public'].'€ ('.$echange.')</a>';
				echo ' <br />';
			}
			
			echo '<input type="radio" name="maj'.$reference.'" value="remboursement" />Remboursement <small>(3)</small><br />';
			echo '<input type="radio" name="maj'.$reference.'" value="echanger_tf" />Echange Top-Fishing <small>(4)</small></td>';
		}	
		
		// Quantité
		echo '<td>'.$quantite.'</td>';
		
		//Prix
		echo	'<td nowrap="nowrap">'.number_format($prix_public, 2, ",", " ").'&nbsp;&euro;</td>';
		echo '<td nowrap="nowrap">'.number_format($prix_public*$quantite, 2, ",", " ").'&nbsp;&euro;</td>';
		echo '</tr>';
	}
?>
	</table>
<?php
if($id_statut == 61 || $id_statut == 62 || $id_statut == 63) {
?>	
	<div class="half">
		<h4>Disponibilité :</h4>
		<p>Il y a plus de 25.000 références pêche en mer sur Top-fishing.fr.<br />
		Les articles les plus vendus sont signalés en stock permanent (environ 20% des articles soit 40% des articles commandés).<br />
		Le reste est délivré par les marques. Nous faisons confiance, aux centrales d’achat des plus grandes marques (Penn, Shimano, Daiwa, Sert, Flashmer, Ultimate Fishing …) pour :</p>
		 <ul>
		 <li>nous transmettre régulièrement des informations correctes sur l’état réel de leurs stocks</li>
		 <li>d'acheminer la livraison des articles en commande dans des délais très courts</li>
		 <li>de signaler les anomalies, rupture et/ou arrêt de fabrication dans des délais brefs.</li>
		 </ul>
		<h4>Modalités :</h4>
		<p>  (1) Attendre que cet article soit disponible pour envoyer la commande. <br />
		(2) Echanger contre un article similaire de votre choix, de valeur et de capacit&eacute; &eacute;quivalente.<br />
		(3) Obtenir un remboursement par Ch&egrave;que pour cet article.<br />
		(4) Laisser Top Fishing vous envoyer un article à la place.</p>
	</div>
	<div class="half">
		<h4>Vos remarques :</h4>
		<p><textarea rows="5" cols="50" name="note-maj"></textarea></p>
		<input type="hidden" name="idcommande" value="<? echo $idcommande; ?>"/>
		<p><input type="submit" value="Informer Top Fishing de vos choix"/></p>
	</div>
<?php } ?>
	</form>
<?php
}
?>
	<hr class="separation" />

</div>

<?php			
//••••••••••••••••••••••••••••••••••••••••••••••••••	
// Alerte client
//••••••••••••••••••••••••••••••••••••••••••••••••••	
?>
	<form method="post"  action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="action" >
			<h3>Autres actions</h3>
			<label for="satisfaction">Votre avis</label>
			<input type="hidden" name="id_commande" value="<?php echo $idcommande ?>" />
			<input type="hidden" name="action" value="alerte" />
			<select name="satisfaction">
				<option value="1" <?php if($satisfaction ==1) echo " selected ";?>>:-D  = Très satisfait</option>
				<option value="2" <?php if($satisfaction ==2) echo " selected ";?>>:-)  = Plutôt satisfait</option>
				<option value="3" <?php if($satisfaction ==3) echo " selected ";?>>:-?  = Je m'impatiente</option>
				<option value="4" <?php if($satisfaction ==4) echo " selected ";?>>:-(  = Mécontent</option>
			</select> <input type="submit" value="Obtenir des infos sur ma commande" /></p>
	</form>	
	<hr class="separation" />
</div>
