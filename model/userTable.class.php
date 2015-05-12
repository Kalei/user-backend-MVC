<?php

/**
 * Description of userTable
 * @author Jean-Loup
 */
class userTable {

     public static function getUserById($id_user) {
          global $pdo;

          $sql = "SELECT * FROM user WHERE id_user='" . $id_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new User($value);
          }
          return $utable[0];
     }

     public static function getUserByIdentifiant($identifiant) {
          global $pdo;

          $sql = "SELECT * FROM user WHERE identifiant='" . $identifiant . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new User($value);
          }
          return $utable[0];
     }

     public static function getAllUsers($actif = true) {
          global $pdo;

          $sql = "SELECT * FROM user ";

          $sql .= ($actif == true) ? " WHERE actif=1 " : "";

          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new User($value);
          }
          return $utable;
     }

     public static function getIdentificationStatue($identifiant, $password) {
          global $pdo;

          $sql = "SELECT * FROM user WHERE identifiant='$identifiant' AND password LIKE '" . md5(md5($password)) . "' AND actif=1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res)) return false;
		 //if(md5(md5($password)) == $res[0]['password']) 
		 return new User($res[0]);
     }

}

?>