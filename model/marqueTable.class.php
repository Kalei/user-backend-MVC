<?php

/**
 * Description of marqueTable
 *
 * @author Rovelli
 */
class marqueTable {

    public static function getMarqueById($id) {
          global $pdo;

          $sql = "SELECT * FROM marque WHERE idmarque=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new Marque($res[0]);
     }

     public static function getAllMarque() {
          global $pdo;

          $sql = "SELECT * FROM marque";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false) {
               return false;
          }

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Marque($value);
          }

          return $utable;
     }

     public static function getIdMarqueFromUrl($url) {
          if ($url != null) {
               global $pdo;

               $sql = "SELECT idmarque FROM marque WHERE url_marque='" . $url . "'";
               $stmt = $pdo->query($sql);
               $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

               if (count($res) == 0)
                    return false;

               return $res[0]['idmarque'];
          }
     }

}
?>