<?php

/**
 * Description of ligneCommandeTable
 *
 * @author Rovelli
 */
class ligneCommandeTable {
    //put your code here
	
	
	public static function getligneCommandeById($id_ligne) {
		global $pdo;
	
		$sql = "SELECT * FROM lignecommande WHERE idlignecommande=" . $id_ligne. " ";
		$sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable[0];
	}
	public static function getlignesByIdCde($idcommande) {
		global $pdo;		
		
		$sql = "SELECT * FROM lignecommande WHERE idcommande='".$idcommande."'";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	
	}
	
	// Lignes commandes Client à expédier (non traités, en cours de réception, en stock)
	public static function getLignesDelivereableByRef($reference) {
		global $pdo;
		if(!isset($reference)) return false;
		$sql = "SELECT l.* 
		FROM lignecommande l, commande c  
			WHERE 
				l.idcommande = c.idcommande 
				AND c.idetat=1
				AND	l.id_etat_ligne_commande<31 
				AND l.idcommande>24000
				AND l.reference=".$reference;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	}
	// Lignes commandes Client à expédier (non traités, en cours de réception, en stock)
	public static function getLignesReceptionnableByRef($reference) {
		global $pdo;
		if(!isset($reference)) return false;
		$sql = "SELECT l.* 
		FROM lignecommande l, commande c  
			WHERE 
				l.idcommande = c.idcommande 
				AND c.idetat=1
				AND	l.id_etat_ligne_commande<31 
				AND l.id_etat_ligne_commande!=11
				AND l.id_etat_ligne_commande!=12
				AND l.idcommande>24000
				AND l.reference=".$reference;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	}
	public static function getLignesDelivereable() {
		global $pdo;
	
		echo $sql = "SELECT l.* 
		FROM lignecommande l, commande c  
			WHERE 
				l.idcommande = c.idcommande 
				AND c.idetat=1
				AND	l.id_etat_ligne_commande<31 
				AND l.idcommande>24000";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	}
	
	public static function getLignesReceptionnable() {
		global $pdo;
	
		$sql = "SELECT l.* 
		FROM lignecommande l, commande c  
			WHERE 
				l.idcommande = c.idcommande 
				AND c.idetat=1
				AND	l.id_etat_ligne_commande<31
				AND l.id_etat_ligne_commande!=11
				AND l.id_etat_ligne_commande!=12
				AND l.idcommande>24000";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	}
	

	
	public static function getNbLigneDelivereable($reference) {
		global $pdo;
	
		$sql = "SELECT l.* 
		FROM lignecommande l, commande c  
			WHERE 
				l.idcommande = c.idcommande 
				AND c.idetat=1
				AND	l.id_etat_ligne_commande<31 
				AND l.idcommande>24000
				AND reference = '".$reference."'";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		$total = 0;
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
			$total += $utable[$key]->quantite;
		}
		return $total;
	}
	
	public static function getLignesPretesByIdCde($idcommande) {
		global $pdo;
	
		$sql = "SELECT * FROM lignecommande 
				WHERE (id_etat_ligne_commande =11 OR id_etat_ligne_commande =12 ) AND idcommande = '".$idcommande."'";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0) return false;
		
		$utable = array();
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCommande($value);
		}
		return $utable;
	}
	
}
