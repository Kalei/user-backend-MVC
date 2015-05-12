<?php

require_once('../includes/start2.php');

function myAutoloader($class) {
     require_once '../includes/model/' . $class . '.class.php';
}

spl_autoload_register('myAutoloader');

$footer = array();
$html_head = array();
$html_head['title'] = "Facture en cours - Top Fishing";
$html_head['css'][] = '<link href="/css/fiche-produit.css" rel="stylesheet" type="text/css" />';
$html_head['css'][] = '<link href="/css/commande.css" rel="stylesheet" type="text/css" />'
        . ' <style type="text/css"> h3{margin-top:0;}</style>';
$html_head['display_error'] = TRUE;
$footer['bootstrap'] = TRUE;
$html_head['js'][] = '';
$rubrique = 'gestion';
//$submenu = 'view/menu.php';
//VARIABLE GLOBALES - Utilisation dans fonction gloabal $nom_variable
$alert = '';
$context = $_POST;

include './controller/controller-context.php';

if (!isset($_SESSION['id_user'])) {
     $desable_menu = TRUE;
}

include '../includes/entete_new.php';

$debug = true;




