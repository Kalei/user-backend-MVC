<?php

/**
 * Description of inventaireTable
 *
 * @author Jean-Loup
 */
class referenceTable {

	public static function getReferenceById($reference) {
		global $pdo;
	
		$sql = "SELECT * FROM article WHERE reference='" . $reference. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Reference($value);
		}
		return $utable[0];
	}
	
	public static function getReferencesByEAN($code_EAN) {
		global $pdo;
		
		$sql = "SELECT * FROM article WHERE code_EAN='" . $code_EAN. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Reference($value);
		}
		return $utable;
	}
	
	public static function getReferencesByCF($code_fournisseur) {
		global $pdo;
	
		$sql = "SELECT * FROM article WHERE code_fournisseur ='" . $code_fournisseur. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Reference($value);
		}
		return $utable;
	}
 
}
?>