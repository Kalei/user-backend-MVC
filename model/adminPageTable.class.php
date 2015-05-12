<?php

/**
 * Description of roleUserTable
 * @author Hugo
 */
class adminPageTable {

     public static function getAdminPageById($id_admin_page) {
          global $pdo;

          $sql = "SELECT * FROM admin_page WHERE id_admin_page='" . $id_admin_page . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new AdminPage($res[0]);
     }

     public static function getAdminPageByLibelle($libelle) {
          global $pdo;

          $sql = "SELECT * FROM admin_page WHERE libelle='" . $libelle . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new AdminPage($res[0]);
     }

     public static function getAdminPageByUrl($url) {
          global $pdo;

          $sql = "SELECT * FROM admin_page WHERE url='" . $url . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new AdminPage($res[0]);
     }

     public static function getAdminPageByGroupe($url) {
          global $pdo;

          $sql = "SELECT * FROM admin_page WHERE url='" . $url . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPage($value);
          }

          return $utable;
     }

     public static function getAllAdminPage() {
          global $pdo;

          $sql = "SELECT * FROM admin_page ";

          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPage($value);
          }
          return $utable;
     }

     public static function getEnumGroupeValues($table) {
          global $pdo;

          $sql = "SHOW COLUMNS FROM $table WHERE Field = 'groupe'";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          preg_match("/^enum\(\'(.*)\'\)$/", $res[0]['Type'], $matches);
          $enum = explode("','", $matches[1]);
          return $enum;
     }

}

?>