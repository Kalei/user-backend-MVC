<?php

/**
 * Description of commandeTable
 *
 * @author Jean-Loup
 */
class commandeTable {

    
	  public static function getCommandeById($id) {
          global $pdo;

          $sql = "SELECT * FROM commande WHERE idcommande=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new Commande($res[0]);
     }
	public static function getCommandesByIdStatut($id) {
		global $pdo;
		if(!isset($id)) return false;
		
		$sql = "SELECT * FROM commande WHERE id_statut=$id AND idcommande>24000";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Commande($value);
		}
		return $utable;
	}
	
	public static function isCdeReadyToShip($idcommande) {
		$lignes = ligneCommandeTable::getlignesByIdCde($idcommande) ;
		$nb_lignes = count($lignes);
		
		$lignes_pretes = ligneCommandeTable::getLignesPretesByIdCde($idcommande) ;
		$nb_lignes_pretes = count($lignes_pretes) ;
		
		if($nb_lignes == $nb_lignes_pretes) return TRUE ;
		else return FALSE ;
	}
	

	public static function getCommandeWithoutIdClientASC() {
          global $pdo;

          $sql = "SELECT * FROM commande WHERE id_client=0 ORDER BY idcommande ASC LIMIT 1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new Commande($res[0]);
     }

     public static function getNbUnknownSoldiers() {
          global $pdo;

          $sql = "SELECT count(*) as nb_soldiers FROM commande WHERE id_client=1";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return $res[0]['nb_soldiers'];
     }
	 
	public static function  getCommandeByDateExpedition($date_debut,$date_fin) {
		global $pdo;
		
		$date_debut = (!empty($date_debut)) ?  $date_debut : date('Y-m-d');
		$date_fin = (!empty($date_fin)) ?  $date_fin : date('Y-m-d');
		$sql_where =  " date_expedition BETWEEN '".$date_debut." 00:00:00  ' AND '".$date_fin." 23:59:59 '" ;

        $sql = "SELECT id_commande FROM expedition WHERE ".$sql_where." AND id_commande>0 ORDER BY date_expedition ASC";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0) return false;

        $utable = array();

        foreach ($res as $commande_number) {
			//echo($commande_number['id_commande']); echo '<br><br>';
			$commande = commandeTable::getCommandeById($commande_number['id_commande']);
			//var_dump($commande);
		  	if($commande == TRUE ) $utable[] = $commande;
        }

        return $utable;
	}

}
