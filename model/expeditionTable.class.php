<?php

/**
 * Description of expeditionTable
 *
 * @author Jean-Loup Couegnas
 */
class expeditionTable {
	
	public static function getExpeditionById($id_expedition) {
		global $pdo;
	
		$sql = "SELECT * FROM expedition WHERE id_expedition=" . $id_expedition. " ";
		$sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Expedition($value);
		}
		return $utable[0];
	}
	public static function getExpeditionsByCde($id_commande) {
		global $pdo;
		
		$sql = "SELECT * FROM expedition WHERE id_commande=".$id_commande." ORDER BY date_expedition ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Expedition($value);
		}
		return $utable;
	}
	public static function getExpeditionsByNumeroColis($numero_colis) {
		global $pdo;
		
		$sql = "SELECT * FROM expedition WHERE numero_colis=".$pdo->quote($numero_colis);
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Expedition($value);
		}
		return $utable;
	}
	
	public static function getExpeditionsByDate($date) {
		global $pdo;
		if(!isset($date)) $date = date('Y-m-d');
		$sql = "SELECT * FROM expedition WHERE date_expedition=".$date;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Expedition($value);
		}
		return $utable;
	}
	
}
?>