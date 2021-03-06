<?php

/**
 * Description of caisseTable_2
 *
 * @author Jean-Loup
 */
class caisseTable_2 {

     public static function getLastCaisse($limit) {
          global $pdo;

          $sql = "SELECT * FROM caisse_2 ORDER BY id_caisse DESC LIMIT 0,$limit";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $utable = array();
          foreach ($res as $key => $value) {
               $utable[$key] = new Caisse_2($value);
               $utable[$key]->lignes_caisse = ligneCaisseTable::getLignesByIdCaisse($utable[$key]->getCaisseId());
          }

          return $utable;
     }

     public static function getCaisseById($id) {
          global $pdo;

          $sql = "SELECT * FROM caisse_2 WHERE id_caisse=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $caisse = new Caisse_2($res[0]);
          $caisse->lignes_caisse = ligneCaisseTable::getLignesByIdCaisse($caisse->getCaisseId());
          return $caisse;
     }

     public static function getLastInsertId() {
          global $pdo;

          $sql = "SELECT id_caisse FROM caisse_2 ORDER BY id_caisse DESC LIMIT 0,1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return $res[0]['id_caisse'];
     }

     public static function getCaissesByDateAchat($date_debut, $date_fin) {
          global $pdo;
          $date_debut = (!empty($date_debut)) ? $date_debut : date('Y-m-d');
          $date_fin = (!empty($date_fin)) ? $date_fin : date('Y-m-d');

          $sql = "SELECT * FROM caisse_2 WHERE date_achat BETWEEN '" . $date_debut . " 00:00:00'  AND '" . $date_fin . "  23:59:00' ORDER BY date_achat ASC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Caisse_2($value);
          }
          return $utable;
     }

     public static function getCaisseByIdUser($id_user, $limit = 5) {
          global $pdo;

          $sql = "SELECT * FROM caisse_2 WHERE id_user=" . $id_user." LIMIT 0,$limit";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Caisse_2($value);
          }
          return $utable;
     }

}
