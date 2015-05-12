<?php

/********************************************************************
	Auteur : Jean-Loup Couegnas
	Projet : Top-Fishing.fr
	Version : PHP5
	Date de Mise à jour : 21/03/2012
	Objet : Suivi de commande
*********************************************************************/

/********************************************************************
			Structure de la page
********************************************************************/

$start		= '../../includes/structure/start.php';
$entete 		= '../../includes/structure/entete_new.php';
$pied		= '../../includes/structure/pied.php';
$preparation	= 'traitement.php';
$affichage	= 'affichage.php';
include($start) ; // Base de données et fonctions communes
mysql_query("SET NAMES 'utf8'");


/********************************************************************
			Préparation des données de la page
********************************************************************/

$table	= "article";

// on insère les styles, les scripts supplémentaires
$css.='<link rel="stylesheet" type="text/css" media="screen" href="/css/article.css" />';	
$js.='<script type="text/javascript" src="/js/fiche_article.js"></script>';
/*$js.='<script type="text/javascript" src="/js/article.js"></script>';*/
$class_page = "page_article";
$rep_img = "/images_produits/";

$title = " Suivi de la commande Top Fishing n°".$_GET['id_commande'] ;
$ariane= " &gt; Suivi &gt; R&eacute;capitulatif de votre commande  au ".date('d/m/Y');



include($preparation) ; // Base de données et fonctions communes


/********************************************************************
			Affichage de la page
********************************************************************/
 include($entete) 	 ;	// Entête de page
 include($affichage) ;	// Affichage de la page
 include($pied)   	 ;	// Pied de page
?>