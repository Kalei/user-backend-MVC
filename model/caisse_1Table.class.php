<?php

/**
 * Description of caisseTable
 *
 * @author Jean-Loup
 */
class caisseTable {

    
	public static function getCaisseById($id) {
		global $pdo;
		
		$sql = "SELECT * FROM caisse WHERE id_caisse=$id";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (empty($res)) return false;
		
		$caisse = new Caisse($res[0]);
		$caisse->lignes_caisse = ligneCaisseTable::getLignesByIdCaisse($caisse->getCaisseId());
		return $caisse ;
	}
	public static function getLastInsertId() {
		global $pdo;
		
		$sql = "SELECT id_caisse FROM caisse ORDER BY id_caisse DESC LIMIT 0,1";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (empty($res)) return false;
		
		return $res[0]['id_caisse'];
	}
	public static function getCaissesByDateAchat($date_debut,$date_fin) {
		global $pdo;
		$date_debut = (!empty($date_debut)) ?  $date_debut : date('Y-m-d');
		$date_fin = (!empty($date_fin)) ?  $date_fin : date('Y-m-d');
		
		$sql = "SELECT * FROM caisse WHERE date_achat BETWEEN '".$date_debut." 00:00:00'  AND '".$date_fin."  23:59:59' ORDER BY date_achat ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Caisse($value);
		}
		return $utable;
	}

}