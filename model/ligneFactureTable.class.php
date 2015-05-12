<?php

/**
 * Description of ligneFactureTable
 *
 * @author Jean-Loup
 */
class ligneFactureTable {
    //put your code here
	
	
	public static function getLigneById($id_lignefacture) {
		global $pdo;
	
		$sql = "SELECT * FROM lignefacture WHERE id_lignefacture	=" . $id_lignefacture. " ";
		$sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneFacture($value);
		}
		return $utable[0];
	}
	public static function getLignesByIdFacture($id_facture) {
		global $pdo;		
		
		$sql = "SELECT * FROM lignefacture WHERE id_facture='".$id_facture."'";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneFacture($value);
		}
		return $utable;
	
	}
	public static function getLignesByRef($reference) {
		global $pdo;		
		
		$sql = "SELECT * FROM lignefacture WHERE reference='".$reference."'";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneFacture($value);
		}
		return $utable;
	
	}
	
	public static function isLigneAlreadyFactured($source, $id_source, $reference) {
		global $pdo;		
		
		$sql = "
			SELECT l.* 
			FROM lignefacture l, facture f  
			WHERE l.reference='".$reference."' 
				  AND f.source='".$source."' 
				  AND f.id_source='".$id_source."' 
				  AND f.id_facture = l.id_facture ";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) > 0) return true;
		else return false;
		
	}
	
}
