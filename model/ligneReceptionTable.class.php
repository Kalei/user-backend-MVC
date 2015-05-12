<?php

/**
 * Description of receptionTable
 *
 * @author Jean-Loup
 */
class ligneReceptionTable {

	public static function getLigneReceptionById($id_ligne) {
		global $pdo;
	
		$sql = "SELECT * FROM lignereception WHERE id_ligne='" . $id_ligne. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneReception($value);
		}
		return $utable[0];
	}
	
	public static function getLignesByIdReception($id_reception, $order = 'ASC', $limit=FALSE) {
		global $pdo;
		$sql_order = ($order == 'DESC') ? ' ORDER BY id_ligne DESC ' : ' ORDER BY id_ligne ASC ' ;
		$sql_limit = ($limit != FALSE) ? ' LIMIT 0, '.$limit.' ' : '';
	
		$sql = "SELECT * FROM lignereception WHERE id_reception='" . $id_reception. "' ".$sql_order.$sql_limit;
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new LigneReception($value);
		}
		return $utable;
	}
	
   
	public static function getLigneReceptionByValues($reference, $id_reception) {
		global $pdo;
		$sql = " 
			SELECT * 
			FROM  lignereception
			WHERE  reference = '" . $reference."'
				AND id_reception = '" . $id_reception."'" ;

        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new LigneReception($value);
        }
        return $utable[0];
    }

}
?>