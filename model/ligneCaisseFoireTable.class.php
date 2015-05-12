<?php

/**
 * Description of ligneCommandeTable
 *
 * @author Rovelli
 */
class ligneCaisseFoireTable {

     public static function getLigneById($id_lignecaisse) {
          global $pdo;

          $sql = "SELECT * FROM lignecaisse_foire WHERE id_lignecaisse	=" . $id_lignecaisse . " ";
          $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisseFoire($value);
          }
          return $utable[0];
     }
	 
	 

     public static function getLignesByIdCaisseFoire($id_caisse_foire) {
          global $pdo;

          $sql = "SELECT * FROM lignecaisse_foire WHERE id_caisse_foire='" . $id_caisse_foire . "'";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisseFoire($value);
          }
          return $utable;
     }

     public static function getLignesByDate($date_debut, $date_fin) {
          global $pdo;

          $sql = "SELECT l.*
			FROM lignecaisse_foire l, caisse_foire c 
			WHERE l.id_caisse_foire = c.id_caisse_foire 
				AND (date_achat BETWEEN ='" . $date_debut . " 00:00:00'  AND '" . $date_fin . "  23:59:00' )";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new LigneCaisseFoire($value);
          }
          return $utable;
     }
	 
	
	
	public static function getLignesCaisseFoiresOrderableByFournisseur($id_fournisseur) {
		global $pdo;
	
		$sql = "SELECT l.* 
			FROM lignecaisse_foire l, article a, fournisseurs f  
			WHERE 
				l.reference = a.reference
				AND a.idfournisseur = f.id_fournisseur
				AND l.id_etat_ligne_commande = 21
				AND f.id_fournisseur =".$id_fournisseur;
		// echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisseFoire($value);
		}
		return $utable;
	}

     public static function getNbLigneDelivereableByRef($reference) {
          global $pdo;

          $sql = "SELECT sum(quantite) as nb_delivrable
					FROM lignecaisse_foire
			WHERE id_etat_ligne_commande < 41 AND reference = '" . $reference . "'";
          //echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          return $res[0]['nb_delivrable'];
     }
	 public static function getLignesCaisseFoiresReceptionnableByRef($reference) {
		global $pdo;
	
		$sql = "SELECT l.* 
		FROM lignecaisse_foire l, caisse_foire c  
			WHERE 
				l.id_caisse_foire = c.id_caisse_foire 
				AND c.id_statut_caisse!=51
				AND	l.id_etat_ligne_commande<41
				AND l.id_etat_ligne_commande!=11
				AND l.id_etat_ligne_commande!=12
				AND l.reference=".$reference;

		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisseFoire($value);
		}
		return $utable;
	} 
	 public static function getLignesCaisseFoiresReceptionnable() {
		global $pdo;
	
		$sql = "SELECT l.* 
		FROM lignecaisse_foire l, caisse_foire c  
			WHERE 
				l.id_caisse_foire = c.id_caisse_foire 
				AND c.id_statut_caisse!=51
				AND	l.id_etat_ligne_commande<41
				AND l.id_etat_ligne_commande!=11
				AND l.id_etat_ligne_commande!=12";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneCaisseFoire($value);
		}
		return $utable;
	}

}
