<?php

/**
 * Description of factureTable
 *
 * @author Jean-Loup Couegnas
 */
class factureTable {

    
	public static function getFactureById($id) {
		global $pdo;
		
		$sql = "SELECT * FROM facture WHERE id_facture=$id";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (empty($res)) return false;
		
		$facture = new Facture($res[0]);
		$facture->lignes_facture = ligneFactureTable::getLignesByIdFacture($facture->getFactureId());
		return $facture ;
	}
	
	public static function getFactureByIdMD5($key) {
          global $pdo;

          $sql = "SELECT * FROM facture where MD5(id_facture) like '%$key%';";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if (count($res) == 0)
               return false;

        $facture = new Facture($res[0]);
		$facture->lignes_facture = ligneFactureTable::getLignesByIdFacture($facture->getFactureId());
		return $facture ;
	}
	
	public static function getLastInsertId() {
		global $pdo;
		
		$sql = "SELECT id_facture FROM facture ORDER BY id_facture DESC LIMIT 0,1";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (empty($res)) return false;
		
		return $res[0]['id_facture'];
	}
	public static function getFacturesByIdClient($id_client) {
		global $pdo;
		
		$sql = "SELECT * FROM facture WHERE id_client=".$id_client." ORDER BY date_facture ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Facture($value);
		}
		return $utable;
	}
	
	public static function getFacturesBySources($source,$id_source) {
		global $pdo;
		
		$sql = "SELECT * FROM facture WHERE id_source=".$id_source." AND source='".$source."' ORDER BY date_facture ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Facture($value);
		}
		return $utable;
	}
	public static function getFacturesByDateFacture($date_debut,$date_fin, $exoneration_tva = 0) {
		global $pdo;
		$date_debut = (!empty($date_debut)) ?  $date_debut : date('Y-m-d');
		$date_fin = (!empty($date_fin)) ?  $date_fin : date('Y-m-d');
		if($exoneration_tva ==1) $sql_where_exoneration_tva =   " AND exoneration_tva=1 " ;
		else if($exoneration_tva ==9) $sql_where_exoneration_tva =   " AND exoneration_tva!=1 " ;
		else $sql_where_exoneration_tva = '';
		
		$sql = "SELECT * FROM facture WHERE date_facture BETWEEN '".$date_debut." 00:00:00'  AND '".$date_fin."  23:59:59' ".$sql_where_exoneration_tva." ORDER BY date_facture ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Facture($value);
		}
		return $utable;
	}

}