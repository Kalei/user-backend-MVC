<?php

/**
 * Description of photosTable
 *
 * @author Rovelli
 */
class photosTable {

     public static function getPhotosById($id) {
          global $pdo;

          $sql = "SELECT * FROM photos WHERE id_photo=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if (empty($res))
               return false;

          return new Photos($res[0]);
     }

     public static function getPhotosByNom($nom) {
          global $pdo;

          $sql = "SELECT * FROM photos WHERE nom=$nom";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new Photos($res[0]);
     }

}
