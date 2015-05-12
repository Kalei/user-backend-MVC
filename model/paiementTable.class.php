<?php

/**
 * Description of paiementTable
 *
 * @author Hugo Rovelli
 */
class paiementTable {

     public static function getPaiementById($id) {
          global $pdo;

          $sql = "SELECT * FROM paiement WHERE id_paiement=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Paiement($value);
          }

          return $utable;
     }
	 public static function getPaiementsBySource($source,$id_source) {
		global $pdo;
		
		$sql = "SELECT * FROM paiement WHERE id_source=".$id_source." AND source='".$source."' ORDER BY date_paiement ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Paiement($value);
		}
		return $utable;
	}

     

     public static function getPaiements() {
          global $pdo;

          $sql = "SELECT * FROM paiement";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($res === false) {
               return false;
          }

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Paiement($value);
          }

          return $utable;
     }

     public static function getTypePaiementLibelles() {
          global $pdo;

          $stmt = $pdo->query("SHOW COLUMNS FROM paiement WHERE Field = 'type_paiement'");
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
         
          preg_match("/^enum\(\'(.*)\'\)$/", $res[0]['Type'], $matches);
          $enum = explode("','", $matches[1]);

          return $enum;
     }

}
