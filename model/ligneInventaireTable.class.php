<?php

/**
 * Description of inventaireTable
 *
 * @author Jean-Loup
 */
class ligneInventaireTable {

	public static function getLigneInventaireById($id_ligne) {
		global $pdo;
	
		$sql = "SELECT * FROM ligneinventaire WHERE id_ligne='" . $id_ligne. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneInventaire($value);
		}
		return $utable[0];
	}
	
	public static function getLignesByIdInventaire($id_inventaire, $order = 'ASC', $limit=FALSE) {
		global $pdo;
		$sql_order = ($order == 'DESC') ? ' ORDER BY id_ligne DESC ' : ' ORDER BY id_ligne ASC ' ;
		$sql_limit = ($limit != FALSE) ? ' LIMIT 0, '.$limit.' ' : '';
	
		$sql = "SELECT * FROM ligneinventaire WHERE id_inventaire='" . $id_inventaire. "' ".$sql_order.$sql_limit;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneInventaire($value);
		}
		return $utable;
	}
	
   
	public static function getLigneInventaireByValues($reference, $id_inventaire) {
		global $pdo;
		$sql = " 
			SELECT * 
			FROM  ligneinventaire
			WHERE  reference = '" . $reference."'
				AND id_inventaire = '" . $id_inventaire."'" ;

        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new LigneInventaire($value);
        }
        return $utable[0];
    }

}
?>