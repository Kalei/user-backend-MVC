<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h3>Historique de dernières caisses</h3>
        <?php
        if (isset($_GET['limit']) && !empty($_GET['limit'])) {
             $limit = $_GET['limit'];
        } else {
             $limit = 5;
        }

        $historique_caisses = caisseTable::getCaisseByIdUser($current_user->id_user,$limit);
        
        if ($historique_caisses != FALSE && count($historique_caisses) > 0) {
             ?>
             <table style="width: 100%;">
                 <tr style="border-bottom: solid 1px; background-color: #f0f0f0; ">
                     <th>Caisse n°</th>
                     <th>Client</th>
                     <th>User</th>
                     <th>vente</th>
                     <th>remise</th>
                     <th>tva</th>
                     <th>tva10</th>
                     <th>tva20</th>
                     <th>Date</th>
                     <th>Statut caisse</th>
                     <th>Action</th>
                 </tr>
                 <?php
                 foreach ($historique_caisses as $historique_caisse) {
                      $historique_client = clientTable::getClientById($historique_caisse->id_client);
                      $historique_user = userTable::getUserById($historique_caisse->id_user);
                      $historique_statut = statutCommandeTable::getStatutCommandeById($historique_caisse->id_statut_caisse);
                      ?>
                      <tr style="border-bottom: solid 1px #e2e2e2;">
                          <td><?php echo $historique_caisse->id_caisse ?></td>
                          <td>
                              <?php
                              if ($historique_client->prenom != 'Inconnu') {
                                   echo $historique_client->prenom . ' ' . $historique_client->nom;
                              } else {
                                   echo 'Non renseigné';
                              }
                              ?>
                          </td>
                          <td><?php echo $historique_user->prenom ?></td>
                          <td><?php echo number_format($historique_caisse->montant_vente_ht, 2, ",", "") ?>€</td>
                          <td><?php echo number_format($historique_caisse->montant_remise_ht, 2, ",", "") ?>€</td>
                          <td><?php echo number_format($historique_caisse->montant_tva, 2, ",", "") ?>€</td>
                          <td><?php echo number_format($historique_caisse->montant_tva10, 2, ",", "") ?>€</td>
                          <td><?php echo number_format($historique_caisse->montant_tva20, 2, ",", "") ?>€</td>
                          <td>
                              <?php
                              $date = date_create_from_format('Y-m-d H:i:s', $historique_caisse->date_achat);
                              echo date_format($date, 'd/m/Y H:i:s');
                              ?>
                          </td>
                          <td><?php echo $historique_statut->libelle_caisse ?></td>
                          <td>
                              <?php
                              if ($historique_caisse->id_statut_caisse == 51) {
                                   echo '<a href="/admin/caisse/?id_caisse=' . $historique_caisse->id_caisse . '" target="_blank">Consulter</a>';
                              } else {
                                   echo '<a href="/admin/caisse/?id_caisse=' . $historique_caisse->id_caisse . '">Consulter/Modifier caisse</a>';
                              }
                              ?>
                          </td>
                      </tr>
                      <?php
                 }
                 ?>
             </table>
             <?php
        } else {
             echo '<div class="alert alert-info">Vous n\'avez aucune caisse dans votre historique.</div>';
        }
        ?>
    </div>
</div>