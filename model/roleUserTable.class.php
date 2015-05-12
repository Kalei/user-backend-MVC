<?php

/**
 * Description of roleUserTable
 * @author Hugo
 */
class roleUserTable {

     public static function getRoleUserById($id_role_user) {
          global $pdo;

          $sql = "SELECT * FROM role_user WHERE id_role_user='" . $id_role_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new RoleUser($res[0]);
     }

     public static function getRoleUserByLibelle($libelle) {
          global $pdo;

          $sql = "SELECT * FROM role_user WHERE libelle='" . $libelle . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new RoleUser($res[0]);
     }

     public static function getAllRoleUser() {
          global $pdo;

          $sql = "SELECT * FROM role_user ";

          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new RoleUser($value);
          }
          return $utable;
     }

}

?>