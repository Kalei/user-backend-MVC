<?php

/**
 * Description of ligneCommandeTable
 *
 * @author Rovelli
 */
class ligneCaisseTable_2 {

     public static function getLigneById($id_lignecaisse) {
          global $pdo;

          $sql = "SELECT * FROM lignecaisse_2 WHERE id_lignecaisse	=" . $id_lignecaisse . " ";
          $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisse_2($value);
          }
          return $utable[0];
     }

     public static function getLignesByIdCaisse($id_caisse) {
          global $pdo;

          $sql = "SELECT * FROM lignecaisse_2 WHERE id_caisse='" . $id_caisse . "'";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisse_2($value);
          }
          return $utable;
     }

     public static function getLignesByDate($date_debut, $date_fin) {
          global $pdo;

          $sql = "SELECT l.*
			FROM lignecaisse_2 l, caisse c 
			WHERE l.id_caisse = c.id_caisse 
				AND (date_achat BETWEEN ='" . $date_debut . " 00:00:00'  AND '" . $date_fin . "  23:59:00' )";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisse_2($value);
          }
          return $utable;
     }

     public static function getNbLigneDelivereable($reference) {
          global $pdo;

          $sql = "SELECT sum(quantite) as nb_delivrable
		FROM lignecaisse_2
			WHERE id_etat_ligne_commande == 12 || id_etat_ligne_commande == 11 reference = '" . $reference . "'";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return $res[0]['nb_delivrable'];
     }

}
