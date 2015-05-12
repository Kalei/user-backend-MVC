<?php
if (isset($view)) {
     if (file_exists('view/' . $view . '.php')) {
          include 'view/' . $view . '.php';
     }else{
          $alert[] = '<div class="alert alert-danger"><strong>Erreur!</strong> Page ou action demandée est inexistante</div>';
          include 'view/default.php';
     }
} else {
     include 'view/default.php';
}