<div class="etape" style="float:right; width: 670px; margin-top: 12px;">
    <h2>Historique commande</h2>
    <?php
    $commandes = commandeTable::getAllCommandsByIdClient($current_user->id_user);

    if ($commandes == false) {
         ?>
         <p style="margin:auto; text-align: center;"><strong>Historique inaccessible</strong><br/><br/>Vous n'avez effectué aucune commande sur notre site...</p>
    <?php } else {
         ?>
         <table id="panier_recap" style="width: 100%">
             <tbody>
                 <tr>
                     <th scope="col">N° Commande</th>
                     <th scope="col">Date Commande</th>
                     <th scope="col">Total TTC</th>
                     <th scope="col">Etat Commande</th>
                     <th scope="col">Actions</th>
                 </tr>
                 <?php
                 foreach ($commandes as $commande) {
                      $phpdate = strtotime($commande->datecde);
                      $mysqldate = date('d-m-Y', $phpdate);
                      ?>
                      <tr>
                          <td><?php echo $commande->idcommande ?></td>
                          <td><?php echo $mysqldate ?></td>                   
                          <td><?php echo $commande->montantcde ?>€</td>
                          <td><?php echo $commande->getLibelleEtatCommande() ?></td>
                          <td>
                              <form action="?action=consulterCommande" method="post" name="coordonnees">
                                  <input type="hidden" name="idcommande" value="<?php echo $commande->idcommande; ?>">
                                  <input type="submit" value="Consulter"/>
                              </form>
                          </td>
                      </tr>
                 <?php }
                 ?>
             </tbody>
         </table>
    <?php }
    ?>
</div>