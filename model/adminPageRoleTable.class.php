<?php

/**
 * Description of adminPageRoleTable
 * @author Hugo
 */
class adminPageRoleTable {

     public static function getAdminPageRoleById($id_admin_page_role) {
          global $pdo;

          $sql = "SELECT * FROM admin_page_role WHERE id_admin_page_role='" . $id_admin_page_role . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return new AdminPageRole($res[0]);
     }

     public static function getAdminPageRoleByIdRoleUser($id_role_user) {
          global $pdo;

          $sql = "SELECT * FROM admin_page_role WHERE id_role_user='" . $id_role_user . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPageRole($value);
          }
          return $utable;
     }

     public static function getAdminPageRoleByIdAdminPage($id_admin_page) {
          global $pdo;

          $sql = "SELECT * FROM admin_page_role WHERE id_admin_page='" . $id_admin_page . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);


          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPageRole($value);
          }
          return $utable;
     }

     public static function getAdminPageRoleByIdRoleUserAndAdminPage($id_role_user, $id_admin_page) {
          global $pdo;

          $sql = "SELECT * FROM admin_page_role WHERE id_role_user='" . $id_role_user . "' AND  id_admin_page='" . $id_admin_page . "' ";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPageRole($value);
          }
          return $utable;
     }

     public static function isAllowedPageForUser($url, $id_user) {
          global $pdo;
          
          $current_page = adminPageTable::getAdminPageByUrl($url);
          $current_affectation = affectationUserTable::getAffectationUserByIdUser($id_user);
          
          if ($current_affectation != FALSE && $current_page != FALSE) {
               $or_role_query = " AND ";

               for ($i = 0; $i < count($current_affectation); $i++) {
                    $or_role_query.= ($i == 0) ? " id_role_user = '" . $current_affectation[$i]['id_role_user'] . "'" : " OR id_role_user = '" . $current_affectation[$i]['id_role_user'] . "'";
               }

               $sql = "SELECT * FROM admin_page_role WHERE id_admin_page='" . $current_page->id_admin_page . "' ";
               //echo $sql;
               $stmt = $pdo->query($sql);
               $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          }

          if (isset($res) && count($res) > 0) {
               return true;
          }

          return false;
     }

     public static function getAllAdminPageRole() {
          global $pdo;

          $sql = "SELECT * FROM admin_page_role ";

          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new AdminPageRole($value);
          }
          return $utable;
     }

}

?>