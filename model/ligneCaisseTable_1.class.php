<?php

/**
 * Description of ligneCommandeTable
 *
 * @author Rovelli
 */
class ligneCaisseTable {
    //put your code here
	
	
	public static function getLigneById($id_lignecaisse) {
		global $pdo;
	
		$sql = "SELECT * FROM lignecaisse WHERE 	id_lignecaisse	=" . $id_lignecaisse. " ";
		$sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisse($value);
		}
		return $utable[0];
	}
	public static function getLignesByIdCaisse($id_caisse) {
		global $pdo;		
		
		$sql = "SELECT * FROM lignecaisse WHERE id_caisse='".$id_caisse."'";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisse($value);
		}
		return $utable;
	
	}
	public static function getLignesByDate($date_debut,$date_fin) {
		global $pdo;		
		
		$sql = "SELECT l.*
			FROM lignecaisse l, caisse c 
			WHERE l.id_caisse = c.id_caisse 
				AND (date_achat BETWEEN ='".$date_debut." 00:00:00'  AND '".$date_fin."  23:59:00' )";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisse($value);
		}
		return $utable;
	
	}
	
	
}
