<?php

class Materiel {

     private $data;
     private $id;

     public function __construct($row, $new = false) {
          global $pdo;
          $i = 0;
          foreach ($row as $key => $value) {
               if ($i == 0 && $value != null && $new == false) {
                    $this->id = $value;
               }
               $this->$key = $value;
               $i++;
          }
     }

     public function save() {
          global $pdo;

          $class = strtolower(get_class($this));

          if (!empty($this->id)) {
               $sql = " UPDATE materiels SET ";
               $set = array();

               foreach ($this->data as $att => $value)
                    if ($att != 'id' && $att != 'id_materiel' && $att != 'children' && $value)
                         $set[] = " $att = " . $pdo->quote($value) . " ";

               $sql .= implode(",", $set);
               $sql .= " WHERE id_materiel =" . $this->id;

               $last_id = $this->id;
               try {
                    $pdo->exec($sql);
               } catch (PDOException $e) {
                    echo $e;
               }
          } else {

               $sql = " INSERT INTO materiels ";
               $sql .= "(" . implode(",", array_keys($this->data)) . ") ";
               foreach (array_values($this->data) as $value)
                    $values[] = $pdo->quote($value);
               $sql .= "values (" . implode(",", array_values($values)) . ")";

               try {
                    $pdo->exec($sql);
               } catch (PDOException $e) {
                    echo "Erreur d'insert : " . $e;
               }

               $query = "SELECT id_materiel FROM materiels ORDER BY id_materiel DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               $last_id = $res[0]["id_materiel"];
          }

          //echo $sql;
          return $last_id == false ? NULL : $last_id;
     }

     public function __get($prop) {
          if ($prop == 'id') {
               return $this->data['id_materiel'];
          } else {
               return htmlspecialchars($this->data[$prop]);
          }
     }

     public function __set($prop, $value) {
          $this->data[$prop] = $value;
     }

     //Moyenne sur l'ensemble des articles correspondants à un id_materiel
     public function setMoyennePoids() {
          global $pdo;

          $sql = "select avg(poids) as moyenne from article WHERE id_materiel1=" . $this->id_materiel . " && poids!=0 ORDER BY reference DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0) {
               $this->poids_moyen = 100;
               return false;
          }

          if ($res[0]['moyenne'] == NULL) {
               $this->poids_moyen = 100;
          } else {
               $this->poids_moyen = intval($res[0]['moyenne']);
          }

          return true;
     }

     //Moyenne sur l'ensemble des articles correspondants à un id_materiel
     public function setMoyenneEncombrement() {
          global $pdo;

          $sql = "select avg(encombrement) as moyenne from article WHERE id_materiel1=" . $this->id_materiel . " && encombrement!=0 ORDER BY reference DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0) {
               return false;
          }

          if ($res[0]['moyenne'] == NULL) {
               $this->encombrement_moyen = 100;
          } else {
               $this->encombrement_moyen = intval($res[0]['moyenne']);
          }

          return true;
     }

     //Moyenne sur l'ensemble des articles correspondants à un id_materiel
     public function setMoyenneVolume() {
          global $pdo;

          $sql = "select avg(volume) as moyenne from article WHERE id_materiel1=" . $this->id_materiel . " && volume!=0 ORDER BY reference DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);


          if (count($res) == 0) {
               return false;
          }

          if ($res[0]['moyenne'] == NULL) {
               $this->volume_moyen = 500;
          } else {
               $this->volume_moyen = intval($res[0]['moyenne']);
          }

          return true;
     }

     public function setAllMoyennes() {
          if ($this->setMoyenneVolume() && $this->setMoyenneEncombrement() && $this->setMoyennePoids()) {
               return true;
          }

          return false;
     }

}

?>