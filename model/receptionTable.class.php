<?php

/**
 * Description of receptionTable
 *
 * @author Jean-Loup
 */
class receptionTable {

	public static function getReceptionById($id_reception) {
		global $pdo;
	
		$sql = "SELECT * FROM reception WHERE id_reception='" . $id_reception. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Reception($value);
		}
		return $utable[0];
	}
   
    
	public static function getAllReceptions($order = false ) {
		global $pdo;
		$sql_order = (isset($order)) ? 'ORDER BY id_reception DESC' : 'ORDER BY id_reception ASC';

        $sql = "SELECT * FROM reception ".$sql_order;

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Reception($value);
        }

        return $utable;
	}

    public static function getReceptionsByIdFournisseur($idfournisseur) {
        global $pdo;

        $sql = "SELECT * FROM reception WHERE id_fournisseur = " . $idfournisseur . "' ";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Reception($value);
        }

        return $utable;
    }
    
	public static function setMontantById($id_reception){
		global $pdo;

		
		$sql = "SELECT * FROM reception WHERE id_reception='" . $id_reception. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($res) != 1)
			return false;
		
		$sql2 = "SELECT SUM(valeur*quantite) as montant FROM lignereception WHERE id_reception='" . $id_reception. "' ";
		$stmt2 = $pdo->query($sql2);
		$res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res2) == 0) 
			return false;
		else {
			$sql3 = "UPDATE reception SET montant='".$res2[0]['montant']."'  WHERE id_reception='" . $id_reception. "' ";
			if($pdo->exec($sql3)) 
				return $res2[0]['montant'];
			else return false;
		}
	}
	public static function setReceptionClos($id_reception){
		global $pdo;
				
		$sql = "UPDATE reception SET etat=2 WHERE id_reception='" . $id_reception. "' ";
		if($pdo->exec($sql)) return TRUE;
		else return FALSE;
	}
	
}
?>