<?php

/********************************************************************
	Auteur : Jean-Loup Couegnas
	Projet : Top-Fishing.fr
	Version : PHP5
	Date de Mise à jour : 21/03/2012
	Objet : Suivi de commande Traitement
*********************************************************************/

//••••••••••••••••••••••••••••••••••••••••••••••••••
// Test de sécurité
//••••••••••••••••••••••••••••••••••••••••••••••••••
// Par défaut la sécurité est fausse
$secu = FALSE ;

if (empty($_GET['nom']) || empty($_GET['prenom']) || empty($_GET['id_commande'])) {
	echo "Oups ! Nous ne pouvons afficher cette commande. Nous contacter." ;
	exit ;
}
else if(!empty($_GET['nom']) && !empty($_GET['prenom']) && !empty($_GET['id_commande'])) {
	
	// Vérif infos associés à idcommande
	$idcommande= mysql_escape_string($_GET['id_commande']);
	$sql_verif_com = "SELECT nom, prenom from commande WHERE idcommande=".$idcommande;
	$res_verif_com =mysql_query($sql_verif_com);
	$don_verif_com = mysql_fetch_assoc($res_verif_com);
	
	// Vérif infos associés à idcommande
	$nom= mysql_real_escape_string($_GET['nom']);
	if(mysql_real_escape_string($don_verif_com['nom'])==$nom) $secu=TRUE;
	/* if($donnees_idcommande['prenom']!=$prenom)  $secu=FALSE; }*/
}



if($secu==FALSE) {
	echo "Oups ! Nous ne pouvons afficher cette commande. Nous contacter." ;
	exit;
}
else {
	$alertok = '<big class="ok">√</big> Vous avez été authentifié.<br />';
	
//••••••••••••••••••••••••••••••••••••••••••••••••••
// 1) si on a demandé une mise à jour de la commande
//••••••••••••••••••••••••••••••••••••••••••••••••••
	if(!empty($_POST['modif-commande'])) {
		require('maj-commande-mail.php');
		$alertok .= '<p><big class="ok">√</big>Les modifications ont été envoyées à Top Fishing. Merci.</p>'
				.'<p>Vous recevrez un email de confirmation sous 48h.</p>';
	}
	if($_POST['action']== "alerte") {
		// On créé une alerte pour la commande
		$satisfaction = (!empty($_POST['satisfaction'])) ? ",  satisfaction = ".$_POST['satisfaction'] : "" ;
		$sql_up_com = "UPDATE commande SET alerte=1 ".$satisfaction." WHERE idcommande=".$idcommande;
		mysql_query($sql_up_com);
		$alertok.= '<big class="ok">√</big> Top Fishing a été informé de votre demande.<br />';
		
		// On On envoi un mail pour la commande
	}
}
?>