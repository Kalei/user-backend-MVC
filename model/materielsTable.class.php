<?php

class materielsTable {

     public static function getFeuilleOrNoeudByUrl($url) {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE url_materiel='" . $url . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) > 0) {
               if (materielsTable::isParent($res[0]['id_materiel'])) {
                    return new NoeudMateriel($res[0]);
               } else {
                    return new FeuilleMateriel($res[0]);
               }
          }

          return false;
     }

     public static function getFeuilleOrNoeudCategorieByUrl($url) {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE url_materiel='" . $url . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) > 0) {
               if (materielsTable::isParent($res[0]['id_materiel'])) {
                    return new NoeudCategorie($res[0]);
               } else {
                    return new FeuilleCategorie($res[0]);
               }
          }

          return false;
     }

     public static function getMaterielsById($id) {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE id_materiel=$id";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) > 0)
               return new Materiel($res[0]);
          return false;
     }

     public static function getMaterielsDesc() {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE lft!=0 AND rght!=0 AND actif_materiel=1 ORDER BY lft DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Materiel($value);
          }

          return $utable;
     }

     public static function getMaterielsAsc() {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE lft!=0 AND rght!=0 AND actif_materiel=1  ORDER BY lft ASC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Materiel($value);
          }

          return $utable;
     }

     public static function getGrandPatron() {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE id_parent=0;";
          // echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          return new Materiel($res[0]);
     }

     public static function isParent($id_parent) {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE id_parent= '" . $id_parent . "'";
          $stmt = $pdo->query($sql);
          $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

          //var_dump($children);
          // cas n° 1 : il n'y pas d'enfant
          if (count($children) == 0)
               return false;

          return true;
     }

     public static function getNoeuds($champs = '*') {

          global $pdo;
          $sql = "SELECT $champs FROM  `materiels` WHERE (rght - lft) >1";
          //$sql = "SELECT * FROM materiels WHERE id_materiel IN (SELECT distinct(id_parent) FROM materiels) ORDER BY niveau ASC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               if ($champs == '*')
                    $utable[$key] = new NoeudMateriel($value);
               else
                    $utable[$key] = $value;
          }

          return $utable;
     }

     public static function getNoeudsByLevel($level) {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE id_materiel IN (SELECT distinct(id_parent) FROM materiels) AND niveau=$level";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new NoeudMateriel($value);
          }

          return $utable;
     }

     public static function getChildByIdNoeud($id) {
          global $pdo;
          $sql = "SELECT * FROM materiels WHERE id_parent=$id ORDER BY lft DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Materiel($value);
          }

          return $utable;
     }

     public static function getDistinctLevel() {

          global $pdo;

          $sql = "SELECT disctinct(niveau) FROM materiels";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Materiel($value);
          }

          return $utable;
     }

     public static function getAllFeuille() {
          global $pdo;

          $sql = "SELECT * FROM materiels WHERE rght-lft = 1 AND is_actif = 1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new FeuilleMateriel($value);
          }

          return $utable;
     }

     public static function getIdMaterielFromUrl($url) {
          if ($url != null) {
               global $pdo;

               $sql = "SELECT id_materiel FROM materiels WHERE url_materiel='" . $url . "'";
               $stmt = $pdo->query($sql);
               $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

               if (count($res) == 0)
                    return false;

               return $res[0]['id_materiel'];
          }
     }
}
