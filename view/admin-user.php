<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3>Enregistrement d'un nouveau user</h3>
        <form action="index.php?action=createUser" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-3">   
                    <label for="user_nom" class="control-label">Nom</label>
                    <input type="text" class="form-control" name="user_nom" id="user_nom" placeholder="Nom du nouveau user">
                </div>
                <div class="col-sm-3">
                    <label for="user_prenom" class="control-label">Prénom</label>
                    <input type="text" class="form-control" name="user_prenom" id="user_prenom" placeholder="Prénom du nouveau user">
                </div>
                <div class="col-sm-3">
                    <label for="libelle_role" class="control-label">Affectation principale</label>
                    <select type="text" name="libelle_role" class="form-control" >
                        <?php
                        $role_list = roleUserTable::getAllRoleUser();
                        if ($role_list != FALSE) {
                             foreach ($role_list as $role) {
                                  echo '<option value="' . $role->libelle . '">' . $role->libelle . '</option>';
                             }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <button style=" width: 100%; margin-top: 22px;" type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3>Réinitialisation du mot de passe</h3>
        <form action="index.php?action=reinitMdp" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-4">
                    <label for="id_user" class="control-label">User</label>
                    <select id="id_user" name="id_user" class="form-control">
                        <?php
                        $user_list = userTable::getAllUsers();
                        if ($user_list != FALSE) {
                             foreach ($user_list as $user) {
                                  echo '<option value="' . $user->id_user . '">' . $user->prenom . ' ' . $user->nom . '</option>';
                             }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <button style=" width: 100%; margin-top: 22px;" type="submit" class="btn btn-warning">Réinitialiser Mot de passe</button>
                </div>

            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3>Nouvelle affectation</h3>
        <form action="index.php?action=addAffectation" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-4">
                    <label for="id_user" class="control-label">User</label>
                    <select id="id_user" name="id_user" class="form-control">
                        <?php
                        $user_list = userTable::getAllUsers();
                        if ($user_list != FALSE) {
                             foreach ($user_list as $user) {
                                  echo '<option value="' . $user->id_user . '">' . $user->prenom . ' ' . $user->nom . '</option>';
                             }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="libelle_role" class="control-label">Affectation principale</label>
                    <select type="text" name="libelle_role" class="form-control" >
                        <?php
                        $role_list = roleUserTable::getAllRoleUser();
                        if ($role_list != FALSE) {
                             foreach ($role_list as $role) {
                                  echo '<option value="' . $role->libelle . '">' . $role->libelle . '</option>';
                             }
                        }
                        ?>
                    </select>

                </div>
                <div class="col-sm-3 col-sm-offset-1">
                    <button style=" width: 100%; margin-top: 22px;" type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3>Liste user avec affectation</h3>
        <table class="table">
            <thead><tr><th>Prénom</th><th>Nom</th><th>Identifiant</th><th>Affectation(s)</th><th>Admin</th><th style="text-align: right"></th></tr></thead>
            <tbody>
                <?php
                foreach ($user_list as $user) {
                     echo '<tr>';
                     echo "<td>$user->prenom</td>";
                     echo "<td>$user->nom</td>";
                     echo "<td>$user->identifiant</td>";
                     echo "<td>";
                     $user_affectations = $user->getAffectations();


                     if ($user_affectations != FALSE) {
                          echo'<ul class="list-unstyled">';
                          foreach ($user_affectations as $user_affectation) {
                               $tmp_role = $user_affectation->getRoleUser();
                               echo '<li><span style="display:inline-block;">' . $tmp_role->libelle . '</span>'
                               . '<form  style="display:inline-block;margin-left:10px" action="index.php?action=delAffectation" method="post" class="form-horizontal">'
                               . '<input type="hidden" name="id_affectation" value="' . $user_affectation->id_affectation_user . '" />'
                               . '<button  style="vertical-align:middle;  color:#d9534f; border:none; padding:0; display:inline-block; text-align:right;" type="submit"><i class="glyphicon glyphicon-remove"></i></button>'
                               . '</form></li>';
                          }
                          echo'</ul>';
                     } else {
                          echo 'Aucune affectation';
                     }
                     echo "</td>";
                     echo "<td>";
                     if ($user->admin == 1) {
                          echo '<img class="img-responsive" width="21px" src="/www/images/icones/admin/check-64.png" alt="Tablestrap" />';
                     } else {
                          echo '<img class="img-responsive" width="21px" src="/www/images/icones/admin/delete_2.png" alt="Tablestrap" />';
                     }
                     echo "</td>";
                     echo '<td style="text-align: right"><form action="index.php?action=desactiveUser" method="post" class="form-horizontal">'
                     . '<input type="hidden" name="id_user" value="' . $user->id_user . '"/>'
                     . '<button type="submit" class="btn btn-danger">Désactiver</button></form>'
                     . '</td>';
                     echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>