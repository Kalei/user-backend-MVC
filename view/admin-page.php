<div class = "row">
    <div class = "col-md-8 col-md-offset-2">
        <h3>Ajouter une page</h3>
        <form action="index.php?action=createAdminPage" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-3">   
                    <label for="page_libelle" class="control-label">Libelle</label>
                    <input type="text" class="form-control" name="page_libelle" id="user_nom" placeholder="Libelle page...">
                </div>
                <div class="col-sm-3">
                    <label for="page_url" class="control-label">Url</label>
                    <input type="text" class="form-control" name="page_url" id="user_prenom" placeholder="Url page (ex: /admin/user/index.php)">
                </div>
                <div class="col-sm-2">
                    <label for="groupe_value" class="control-label">Groupe (menu)</label>
                    <select class="form-control" name="groupe_value">
                        <option value="">Aucun groupe</option>
                        <?php
                        foreach (adminPageTable::getEnumGroupeValues('admin_page') as $enum) {
                             echo '<option value="' . $enum . '">' . ucfirst($enum) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label for="id_role_user" class="control-label">Accès par défault</label>
                    <select type="text" name="id_role_user" class="form-control" >
                        <?php
                        $role_list = roleUserTable::getAllRoleUser();
                        if ($role_list != FALSE) {
                             foreach ($role_list as $role) {
                                  echo '<option value = "' . $role->id_role_user . '">' . $role->libelle . '</option>';
                             }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button style=" width: 100%; margin-top: 22px;" type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class = "row">
    <div class = "col-md-8 col-md-offset-2">
        <h3>Droit d'accès page/rôle</h3>
        <form action="index.php?action=assignPageRole" method="post" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-3">
                    <label for="id_page" class="control-label">Page</label>
                    <select type="text" name="id_page" class="form-control" >
                        <?php
                        $page_list = adminPageTable::getAllAdminPage();
                        if ($page_list != FALSE) {
                             foreach ($page_list as $page) {
                                  echo '<option value = "' . $page->id_admin_page . '">' . $page->libelle . '</option>';
                             }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="id_role" class="control-label">Droit d'accès (rôle)</label>
                    <select type="text" name="id_role" class="form-control" >
                        <?php
                        if ($role_list != FALSE) {
                             foreach ($role_list as $role) {
                                  echo '<option value = "' . $role->id_role_user . '">' . $role->libelle . '</option>';
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
        <h3>Listing page et droits d'accès</h3>
        <table class="table">
            <thead><tr><th>Libelle</th><th>Url</th><th>Groupe</th><th>Accès (rôles)</th><th>Supprimer</th></tr></thead>
            <tbody>
                <?php
                if ($page_list != FALSE) {
                     foreach ($page_list as $page) {
                          echo '<tr>';
                          echo "<td>$page->libelle</td>";
                          echo "<td><a href='/admin/$page->url'>$page->url</a></td>";
                          echo "<td>$page->groupe</td>";
                          echo "<td>";
                          $page_roles = adminPageRoleTable::getAdminPageRoleByIdAdminPage($page->id_admin_page);
                          //svar_dump($page_roles);
                          if ($page_roles != FALSE) {
                               echo'<ul class="list-unstyled">';
                               foreach ($page_roles as $page_role) {
                                    $tmp_role = $page_role->getRoleUser();
                                    echo '<li><span style="display:inline-block;">' . $tmp_role->libelle . '</span>'
                                    . '<form  style="display:inline-block;margin-left:10px" action="index.php?action=delPageRole" method="post" class="form-horizontal">'
                                    . '<input type="hidden" name="id_admin_page_role" value="' . $page_role->id_admin_page_role . '" />'
                                    . '<button  style="vertical-align:middle;  color:#d9534f; border:none; padding:0; display:inline-block; text-align:right;" type="submit"><i class="glyphicon glyphicon-remove"></i></button>'
                                    . '</form></li>';
                               }
                               echo'</ul>';
                          } else {
                               echo 'Aucune affectation';
                          }
                          echo "</td>";
                          echo '<td style="text-align: right">'
                          . '<form action="index.php?action=delPage" method="post" class="form-horizontal">'
                          . '<input type="hidden" name="id_admin_page" value="' . $page->id_admin_page . '"/>'
                          . '<button style="" type="submit" class="btn btn-danger">Supprimer indexation</button>'
                          . '</form>'
                          . '</td>';
                          echo '</tr>';
                     }
                }
                else{
                    echo 'Aucune page n\'a été enregistrée';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>