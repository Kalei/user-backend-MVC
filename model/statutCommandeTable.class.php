<?php

/**
 * Description of statutCommandeTable
 *
 * @author Hugo Rovelli
 */
class statutCommandeTable {

     public static function getStatutCommandeById($id) {
          global $pdo;

          $sql = "SELECT * FROM statut_commande
				WHERE id_statut = $id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          //echo count($res);

          if (count($res) == 0)
               return false;

          return new StatutCommande($res[0]);
     }

     public static function getAllStatutCaisse() {
          global $pdo;

          $sql = "SELECT * FROM statut_commande
				WHERE libelle_caisse != ''";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          //echo count($res);

          if (count($res) == 0)
               return false;

          $utable = array();
          foreach ($res as $key => $value) {
               $utable[$key] = new EtatLigneCommande($value);
          }
          return $utable;
     }

}
