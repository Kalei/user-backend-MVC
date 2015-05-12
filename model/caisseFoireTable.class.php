<?php

/**
 * Description of caisseTable
 *
 * @author Jean-Loup
 */
class caisseFoireTable {

     public static function getLastCaisseFoire($limit) {
          global $pdo;

          $sql = "SELECT * FROM caisse_foire ORDER BY id_caisse_foire DESC LIMIT 0,$limit";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $utable = array();
          foreach ($res as $key => $value) {
               $utable[$key] = new CaisseFoire($value);
               $utable[$key]->lignes_caisse = ligneCaisseFoireTable::getLignesByIdCaisseFoire($utable[$key]->getCaisseFoireId());
          }

          return $utable;
     }

     public static function getCaisseFoireById($id) {
          global $pdo;

          $sql = "SELECT * FROM caisse_foire WHERE id_caisse_foire=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $caisse = new CaisseFoire($res[0]);	
          $caisse->lignes_caisse = ligneCaisseFoireTable::getLignesByIdCaisseFoire($id);
          return $caisse;
     }

     public static function getLastInsertId() {
          global $pdo;

          $sql = "SELECT id_caisse_foire FROM caisse_foire ORDER BY id_caisse_foire DESC LIMIT 0,1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return $res[0]['id_caisse_foire'];
     }

     public static function getCaisseFoiresByDateAchat($date_debut, $date_fin) {
          global $pdo;
          $date_debut = (!empty($date_debut)) ? $date_debut : date('Y-m-d');
          $date_fin = (!empty($date_fin)) ? $date_fin : date('Y-m-d');	 
          $sql = "SELECT * FROM caisse_foire WHERE date_achat BETWEEN '" . $date_debut . " 00:00:00'  AND '" . $date_fin . "  23:59:00'  ORDER BY date_achat ASC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new CaisseFoire($value);
          }
          return $utable;
     }
	 public static function getCaisseFoiresByTris($date_debut, $date_fin, $type) {
          global $pdo;
          $date_debut = (!empty($date_debut)) ? $date_debut : date('Y-m-d');
          $date_fin = (!empty($date_fin)) ? $date_fin : date('Y-m-d');
		  if($type == 51) $sql_where = ' AND id_statut_caisse=51'; 	
		  elseif($type == 99) $sql_where = ' AND id_statut_caisse!=51'; 
		  else $sql_where = ''; 	 
          $sql = "SELECT * FROM caisse_foire WHERE date_achat BETWEEN '" . $date_debut . " 00:00:00'  AND '" . $date_fin . "  23:59:00'  ".$sql_where." ORDER BY date_achat ASC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new CaisseFoire($value);
          }
          return $utable;
     }
	 

     public static function getCaisseFoireByIdVendeur($id_vendeur, $limit = 5) {
          global $pdo;

          $sql = "SELECT * FROM caisse_foire WHERE id_vendeur=" . $id_vendeur." LIMIT 0,$limit";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new CaisseFoire($value);
          }
          return $utable;
     }

}
