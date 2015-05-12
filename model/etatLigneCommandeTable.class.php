<?php

/**
 * Description of statutCommandeTable
 *
 * @author Hugo Rovelli
 */
class etatLigneCommandeTable {
     public static function getEtatLigneCommandeCaisseById($id) {
          global $pdo;

          $sql = "SELECT * FROM etatlignecommande 
				WHERE id_etat_ligne_commande = '$id'";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          //echo count($res);

          if (count($res) == 0)
               return false;

          return new StatutCommande($res[0]);
     }
     
     public static function getSelectableEtatLigneCommandeCaisse() {
          global $pdo;

          $sql = "SELECT * FROM etatlignecommande 
				WHERE id_etat_ligne_commande = 52 || id_etat_ligne_commande = 21";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          //echo count($res);

          if (count($res) == 0)
               return false;

          $utable = array();
          foreach ($res as $key => $value) {
               $utable[$key] = new StatutCommande($value);
          }
          return $utable;
     }

}
