<?php

/**
 * Description of userTable
 * @author Jean-Loup
 */
class affectationUserTable {

     public static function getAffectationUserById($id_affectation_user) {
          global $pdo;

          $sql = "SELECT * FROM affectation_user WHERE id_affectation_user='" . $id_affectation_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new AffectationUser($res[0]);
     }

     public static function getAffectationUserByIdUser($id_user) {
          global $pdo;

          $sql = "SELECT * FROM affectation_user WHERE id_user='" . $id_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AffectationUser($value);
          }
          return $utable;
     }

     public static function getAffectationUserByIdRole($id_role_user) {
          global $pdo;

          $sql = "SELECT * FROM affectation_user WHERE id_role_user='" . $id_role_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AffectationUser($value);
          }
          return $utable;
     }
     
     public static function getAllAffectationsUser($actif = true) {
          global $pdo;

          $sql = "SELECT *.affectation_user FROM affectation_user, user";
          
          $sql .= ($actif==true) ? " WHERE user.actif=1 " : "";
          
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AffectationUser($value);
          }
          return $utable;
     }

}

?>