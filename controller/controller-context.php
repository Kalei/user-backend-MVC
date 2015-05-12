<?php

function identificationController() {
     global $context;
     global $current_user;
     global $alert;

     $user_identifiant = isset($context['identifiant']) ? $context['identifiant'] : '';
     $user_password = isset($context['user_password']) ? $context['user_password'] : '';

     $current_user = userTable::getIdentificationStatue($user_identifiant, $user_password);

     if ($current_user != false) {
          $_SESSION['id_user'] = $current_user->id_user;

          if (md5(md5($user_password)) == "ce93d34ea4d11a7ed8044a0bc63b4c76") {
               $alert[] = "<div class='alert alert-warning'><strong>Attention!</strong> Le mot de passe que vous utilisez est celui par défault. "
                       . "<a href='./index.php?action=modif-password'>Merci de le modifier ici...</a></div>";
          }

          return 'identification_ok';
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Identification impossible!</strong> Mot de passe ou identifiant non valide.</div>";
          return 'identification_ko';
     }
}

function logoutController() {
     global $current_user;
     global $alert;

     if (!empty($current_user)) {
          unset($_SESSION['id_user']);
          unset($current_user);
          $alert[] = "<div class='alert alert-success'><strong>Bravo !</strong> Déconnexion réussie.</p>";
          return 'logout_ok';
     } else {
          return 'logout_ko';
     }
}

function testPasswords($password, $verif_pass) {
     global $context;
     global $alert;

     $pass = clearWhitespace($password);
     $pass_verif = clearWhitespace($verif_pass);
     $test_patern = preg_match("((?=.*\d)(?=.*[a-z]).{6,20})", $pass);

     if ($pass == $pass_verif && $test_patern) {
          $context['pass'] = $pass;
          $context['pass_verif'] = $pass_verif;
          return TRUE;
     } else {
          if ($pass != $pass_verif) {
               $alert[] = "<div class='alert alert-error'><strong>Erreur!</strong> Les mots de passes fournis ne sont pas identiques, merci de les saisir de nouveau.</div>";
          } else {
               $alert[] = "<div class='alert alert-error'><strong>Erreur!</strong> Le mot de passe doit être composé au minimum de 6 caractères et contenir au moins un chiffre et une lettre.</div>";
          }
          return FALSE;
     }
}

function modifPasswordController() {
     global $current_user;
     global $context;
     global $alert;

     $old_password = (isset($context['old_password'])) ? $context['old_password'] : NULL;
     if ($current_user->password == md5(md5($old_password))) {
          $new_password = isset($context['new_password']) ? $context['new_password'] : NULL;
          $verif_password = isset($context['verif_password']) ? $context['verif_password'] : NULL;

          if (testPasswords($new_password, $verif_password) == TRUE) {
               $current_user->password = md5(md5($new_password));
               if ($current_user->save() == false) {
                    $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Un problème est survenu lors de l'enregistrement merci de contacter l'administrateur.</div>";
                    return 'modifPassword_ko';
               } else {
                    $alert[] = "<div class='alert alert-success'><strong>Bravo!</strong> Votre mot de passe a bien été mis à jour.</div>";
                    return 'modifPassword_ok';
               }
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Le mot de passe actuel que vous avez renseigné n'est pas le bon.</div>";
          return 'modifPassword_ko';
     }
}

function createUserController() {
     global $context;
     global $alert;

     $user_nom = isset($context['user_nom']) ? $context['user_nom'] : '';
     $user_prenom = isset($context['user_prenom']) ? $context['user_prenom'] : '';
     $libelle_role = isset($context['libelle_role']) ? $context['libelle_role'] : '';

     if (!empty($user_nom) && !empty($user_prenom)) {
          $data = array("nom" => $user_nom, "prenom" => $user_prenom, "identifiant" => strtolower(substr($user_prenom, 0, 1) . "_" . $user_nom));
          $new_user = new User($data, TRUE);
          $id_user = $new_user->save();
          $alert[] = "<div class='alert alert-success'><strong>Félicitation!</strong> L'utilisateur " . $user_nom->nom . " " . $user_nom->prenom . " a bien été créé.</div>";
          if (!empty($libelle_role)) {
               $role = roleUserTable::getRoleUserByLibelle($libelle_role);
               if ($role == FALSE) {
                    $role = new RoleUser(array("libelle" => $libelle_role), TRUE);
                    $id_role = $role->save();
               } else {
                    $id_role = $role->id_role_user;
               }
               $data_affectation = array("id_user" => $id_user, "id_role_user" => $id_role);
               $new_affectation = new AffectationUser($data_affectation, TRUE);
               if ($new_affectation->save() != FALSE) {
                    $alert[] = "<div class='alert alert-success'><strong>" . $user_prenom . " " . $user_nom . " </strong> a été affecté au rôle de <strong>" . $role->libelle . "</strong>.</div>";
                    return 'createUser_ok';
               } else {
                    $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Impossible d'enregistrer l'affectation en base.</div>";
                    return 'createUser_ko';
               }
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> L'enssemble des conditions pour effectuer cette action ne sont pas réunis.</div>";
          return 'createUser_ko';
     }
}

function addAffectationController() {
     global $context;
     global $alert;

     $id_user = isset($context['id_user']) ? $context['id_user'] : '';
     $libelle_role = isset($context['libelle_role']) ? $context['libelle_role'] : '';

     if (!empty($id_user) && !empty($libelle_role)) {
          $user = userTable::getUserById($id_user);
          $role = roleUserTable::getRoleUserByLibelle($libelle_role);
          if ($role == FALSE) {
               $role = new RoleUser(array("libelle" => $libelle_role), TRUE);
               $id_role = $role->save();
          } else {
               $id_role = $role->id_role_user;
          }
          $data_affectation = array("id_user" => $id_user, "id_role_user" => $id_role);
          $new_affectation = new AffectationUser($data_affectation, TRUE);
          if ($new_affectation->save() != FALSE) {
               $alert[] = "<div class='alert alert-success'><strong>" . $user->prenom . " " . $user->nom . " </strong> a été affecté au rôle de <strong>" . $role->libelle . "</strong>.</div>";
               return 'addAffectation_ok';
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Impossible d'enregistrer l'affectation en base.</div>";
               return 'addAffectation_ko';
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong>.</div>";
          return 'addAffectation_ko';
     }
}

function delAffectationController() {
     global $context;
     global $alert;

     $id_affectation = isset($context['id_affectation']) ? $context['id_affectation'] : '';

     if (!empty($id_affectation)) {
          $to_del = affectationUserTable::getAffectationUserById($id_affectation);
          $user = $to_del->getUser();
          $role = $to_del->getRoleUser();

          if ($to_del != false) {
               $delete = $to_del->delete();
               if ($delete == TRUE) {
                    $alert[] = "<div class='alert alert-success'><strong>" . $user->prenom . " " . $user->nom . " </strong> n'est plus affecté au rôle de <strong>" . $role->libelle . "</strong>.</div>";
                    return 'addAffectation_ok';
               }
          }
          $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong>Impossible de supprimer cette affectation.</div>";
          return 'addAffectation_ko';
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> L'enssemble des conditions pour effectuer cette action ne sont pas réunis.</div>";
          return 'addAffectation_ko';
     }
}

function desactiveUserController() {
     global $context;
     global $alert;

     $id_user = isset($context['id_user']) ? $context['id_user'] : '';

     if (!empty($id_user)) {
          $user = userTable::getUserById($id_user);
          if ($user != FALSE) {
               $user->actif = 0;
               if ($user->save() != FALSE) {
                    $alert[] = "<div class='alert alert-success'><strong>" . $user->prenom . " " . $user->nom . " </strong> n'est plus actif.</div>";
                    return 'createUser_ok';
               } else {
                    $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Impossible d'enregistrer le user en base.</div>";
                    return 'createUser_ko';
               }
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> User spécifié non trouvé.</div>";
               return 'createUser_ko';
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Paramètre manquant!</strong>.</div>";
          return 'createUser_ko';
     }
}

function reinitMdpController() {
     global $context;
     global $alert;

     return 'reinitMdp_ko';
}

function createAdminPageController() {
     global $context;
     global $alert;
//var_dump($context);
     if (isset($context['page_url']) && isset($context['page_libelle']) && isset($context['id_role_user']) && isset($context['groupe_value'])) {
          if (adminPageTable::getAdminPageByUrl($context['page_url']) == FALSE) {
               $new_page = new AdminPage(array('libelle' => $context['page_libelle'], 'url' => $context['page_url'], 'groupe' => $context['groupe_value']), true);
               $new_page_id = $new_page->save();
               if ($new_page_id != FALSE) {
                    $alert[] = "<div class='alert alert-success'><strong>Bravo!</strong>La nouvelle page a été crée.</div>";
                    if (assignPageRole($new_page_id, $context['id_role_user'])) {
                         $current_role = roleUserTable::getRoleUserById($context['id_role_user']);
                         $alert[] = "<div class='alert alert-success'><strong>Bravo!</strong>Le utilisateur faisant partie de " . $current_role->libelle . " .</div>";
                    } else {
                         $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong>Assignation role/page impossible.</div>";
                    }
               } else {
                    $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong>Enregistrement en base impossible.</div>";
               }
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong>Cette page est déjà indéxée en base.</div>";
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Paramètre manquant!</strong></div>";
     }

     return 'admin-page';
}

function assignPageRoleController() {
     global $context;
     global $alert;

     if (isset($context['id_role']) && isset($context['id_page'])) {
          if (assignPageRole($context['id_page'], $context['id_role'])) {
               $current_role = roleUserTable::getRoleUserById($context['id_role']);
               $alert[] = "<div class='alert alert-success'><strong>Bravo!</strong> L'utilisateur faisant partie de " . $current_role->libelle . " .</div>";
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Assignation role/page impossible ou déjà définis.</div>";
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Paramètre manquant!</strong></div>";
     }

     return 'admin-page';
}

function assignPageRole($new_page_id, $id_role_user) {
     if (!empty($new_page_id) && !empty($id_role_user) && adminPageRoleTable::getAdminPageRoleByIdRoleUserAndAdminPage($new_page_id, $id_role_user) == FALSE) {
          $new_page_role = new AdminPageRole(array('id_admin_page' => $new_page_id, 'id_role_user' => $id_role_user), true);
          if ($new_page_role->save() != FALSE) {
               return TRUE;
          }
     }
     return FALSE;
}

function delPageRoleController() {
     global $context;
     global $alert;

     if (isset($context['id_admin_page_role'])) {
          $current_page_role = adminPageRoleTable::getAdminPageRoleById($context['id_admin_page_role']);
          if ($current_page_role->delete()) {
               $alert[] = "<div class='alert alert-success'>Suppression des droits d'accès réussie.</div>";
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Problème lors de la suppression.</div>";
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Paramètre(s) manquant!</strong></div>";
     }

     return 'admin-page';
}

function delPageController() {
     global $context;
     global $alert;

     if (isset($context['id_admin_page'])) {
          $current_page = adminPageTable::getAdminPageById($context['id_admin_page']);
          if ($current_page != FALSE) {
               if ($current_page->delete()) {
                    $alert[] = "<div class='alert alert-success'>Suppression de la page réussie.</div>";
               } else {
                    $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Problème lors de la suppression.</div>";
               }
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Page inexistante, suppression impossible.</strong></div>";
          }
     } else {
          $alert[] = "<div class='alert alert-danger'><strong>Paramètre(s) manquant!</strong></div>";
     }

     return 'admin-page';
}

if (isset($_GET['action'])) {
     $action = $_GET['action'];
     try {
          if (($action == "admin-user" || $action == "admin-page") && $current_user->admin != 1) {
               $action = "default";
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> Vous n'avez pas les droits nécessaires pour accéder à cette page</div>";
          }

          $fonction = $action . "Controller";
//echo $fonction;
//On limite les actions connnecté/non-connecté

          if (((empty($current_user) && ($action == 'identification')) || !empty($current_user))) {
               if (function_exists($fonction)) {
                    $view = $fonction();
               } else {
                    $view = $action;
               }
          } else {
               $alert[] = "<div class='alert alert-danger'><strong>Erreur!</strong> L'action que vous avez renseignée n'est pas permise ou introuvable.</div>";
          }
     } catch (Exception $e) {
          echo 'Action interdite : ', $e->getMessage(), "\n";
     }
}     