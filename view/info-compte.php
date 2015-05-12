<div class='row'>
    <div class="col-md-2 col-md-offset-2">
        <ul class="list-unstyled">
            <?php if ($current_user->avatar == null) { ?>
                 <li><img width="100px" alt="Votre image de profil" src="/www/images/avatar/avatar.jpg" /></li>
                 <?php
            } else {
                 echo '<li><img width="100px" alt="Votre image de profil" src="' . $current_user->avatar . '" /></li>';
            }
            ?>
            <li><strong>Prénom: </strong><?php echo $current_user->prenom; ?></li>
            <li><strong>Nom: </strong><?php echo $current_user->nom; ?></li>
        </ul>
    </div>
    <div class="col-md-4 ">
        <p><strong>Vos affectations:</strong></p>
        <ul>
            <?php
            $affectations = affectationUserTable::getAffectationUserByIdUser($current_user->id_user);
            if ($affectations == FALSE) {
                 echo '<p class="alert alert-warning">Aucune affectation ne vous a été assigner contacter l\'adminstrateur.</p>';
            } else {
                 foreach ($affectations as $affectation) {
                      $tmp_role = $affectation->getRoleUser();
                      echo '<li>' . $tmp_role->libelle . '</li>';
                 }
            }
            ?>
        </ul>
    </div>
    <div class="col-md-2">
        <ul class="list-unstyled">
            <li><a href="index.php?action=modif-password">Modifier mot de passe</a><hr/></li>
            <?php
            if ($current_user->admin == 1) {
                 echo '<li><a href = "index.php?action=admin-user">Administration users et rôles</a></li>';
                 echo '<li><a href = "index.php?action=admin-page">Administration droits d\'accès aux pages</a></li>';
            }
            ?>
        </ul>
    </div>
</div>

<?php

/*
 * On mettra ici les historique ou infos a afficher pour l'user
 */

//include 'view/historiqueCaisse.php';
