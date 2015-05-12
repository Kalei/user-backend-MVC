<?php

/**
 * Description of ligneCommandeTable
 *
 * @author Rovelli
 */
class fournisseurLigneCommandeTable {
    //put your code here
	
	
	public static function getFournisseurLigneCommandeById($id_ligne) {
		global $pdo;
	
		$sql = "SELECT * FROM fournisseur_lignecommandes WHERE idlignecommande='" . $id_ligne. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new FournisseurLigneCommande($value);
		}
		return $utable[0];
	}
	public static function getFournisseurLastLigneCommandeByRef($reference) {
		global $pdo;
	
		$sql = "SELECT * FROM fournisseur_lignecommandes WHERE reference='" . $reference. "' ORDER BY id_fournisseur_lignecommande DESC LIMIT 0,1";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new FournisseurLigneCommande($value);
		}
		return $utable[0];
	}
	
	public static function getNbLigneReceiveable($reference) {
		global $pdo;
	
		$sql = "SELECT * FROM fournisseur_lignecommandes WHERE statut_lignecommande =1 AND reference = '".$reference."' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		$total = 0;
		foreach ($res as $key => $value) {
			$utable[$key] = new FournisseurLigneCommande($value);
			$total += ($utable[$key]->quantite_a_commander-$utable[$key]->quantite_recue);
		}
		return $total;
	}
	public static function getLignesReceiveableByRef($reference, $order = 'ASC') {
		global $pdo;
		
		$sql_order = ($order == 'DESC') ? ' ORDER BY id_fournisseur_lignecommande DESC' : ' ORDER BY id_fournisseur_lignecommande ASC' ;
		$sql = "SELECT * FROM fournisseur_lignecommandes WHERE statut_lignecommande =1 AND reference = '".$reference."' ".$sql_order;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		foreach ($res as $key => $value) {
			$utable[$key] = new FournisseurLigneCommande($value);
		}
		return $utable;
	}
	
	public static function getLignesReceiveable() {
		global $pdo;
	
		$sql = "SELECT * FROM fournisseur_lignecommandes WHERE statut_lignecommande =1 ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new FournisseurLigneCommande($value);
		}
		return $utable;
	}
	
	// Méthode => récupère un objet article contenant les données SQL à partir de son nom de modèle
     public static function getFournisseurLignesCommandeByRef($reference) {
          global $pdo;

          $sql = "SELECT * FROM fournisseur_lignecommandes WHERE reference='" . $reference. "' ORDER BY id_fournisseur_lignecommande DESC";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new FournisseurLigneCommande($value);
          }
          return $utable;
     }
	
}
