<?php

/**
 * Description of inventaireTable
 *
 * @author Jean-Loup
 */
class inventaireTable {

	public static function getInventaireById($id_inventaire) {
		global $pdo;
	
		$sql = "SELECT * FROM inventaire WHERE id_inventaire='" . $id_inventaire. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Inventaire($value);
		}
		return $utable[0];
	}
   
	public static function getInventaireByValues($id_materiel = 0, $id_marque = 0) {
		global $pdo;
		if(empty($id_marque))  $id_marque = 0;
		$sql = " 
			SELECT * 
			FROM  inventaire
			WHERE  LIKE '%" . $id_materiel . "%'
				AND id_marque = " . $id_marque ;

        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Inventaire($value);
        }
        return $utable[0];
    }
    
	public static function getAllInventairesDesc() {
		global $pdo;

        $sql = "SELECT * FROM inventaire ORDER BY id_inventaire DESC";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Inventaire($value);
        }

        return $utable;
	}

    public static function getInventairesByIdMateriel($id_materiel) {
        global $pdo;

        $sql = "SELECT * FROM inventaire "
                . " WHERE materiels LIKE '%" . $id_materiel . "%' ";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Inventaire($value);
        }

        return $utable;
    }
    
     public static function getInventairesByIdMarque($id_marque) {
        global $pdo;

        $sql = "SELECT * FROM inventaire "
                . "WHERE id_marque =" . $id_marque . " ";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Inventaire($value);
        }

        return $utable;
    }

	public static function setMontantById($id_inventaire){
		global $pdo;

		
		$sql = "SELECT * FROM inventaire WHERE id_inventaire='" . $id_inventaire. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($res) != 1)
			return false;
		
		$sql2 = "SELECT SUM(valeur*stock) as montant FROM ligneinventaire WHERE id_inventaire='" . $id_inventaire. "' ";
		$stmt2 = $pdo->query($sql2);
		$res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res2) == 0) 
			return false;
		else {
			$sql3 = "UPDATE inventaire SET montant='".$res2[0]['montant']."'  WHERE id_inventaire='" . $id_inventaire. "' ";
			if($pdo->exec($sql3)) 
				return $res2[0]['montant'];
			else return false;
		}
	}
	public static function setInventaireClos($id_inventaire){
		global $pdo;
				
		$sql = "UPDATE inventaire SET etat=0 WHERE id_inventaire='" . $id_inventaire. "' ";
		if($pdo->exec($sql)) return TRUE;
		else return FALSE;
	}
	
}
?>