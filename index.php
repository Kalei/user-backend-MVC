<?php
$debug = true;
include '../includes/start.php';

$context = (!empty($_POST)) ? $_POST : NULL ;

$html_head = array();
$html_head['title'] = "Authentification && Gestion des USERS - Admin Top Fishing";
$html_head['css'][] = '<style type="text/css"> h3{margin-top:0;}</style>';
$html_head['display_error'] = TRUE;
$footer['bootstrap'] = TRUE;
$html_head['js'][] = '';
$rubrique = 'gestion';
$alert = '';

include './controller/controller-context.php';
include '../includes/entete.php';


if (isset($_SESSION['id_user'])) {
     ?>
     <div class="row">
         <div class="col-md-offset-2 col-md-8">
             <p style="color: #0066CC; font-weight: bold;">
                 <a class="btn btn-navbar" href="index.php">
                     <i class="glyphicon glyphicon-home"></i><br/>
                     <strong>Accueil</strong></a> 
                 <i class="glyphicon glyphicon-bullhorn"></i> 
                 Vous retrouverez dans votre espace personnel l'essenciel des informations vous concernant et l'accès à vos outils.</p>
         </div>
     </div>
     <?php
} 

include './controller/controller-view.php';
include '../includes/pied.php';
