<?php

/**
 * Description of ecartStockTable
 *
 * @author Jean-Loup
 */
class ecartStockTable {

	public static function getEcartStockById($id_ecart) {
		global $pdo;
	
		$sql = "SELECT * FROM ecart_stock WHERE id_ecart='" . $id_ecart. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new EcartStock($value);
		}
		return $utable[0];
	}
	public static function getEcartsStock($order, $limit = FALSE) {
		global $pdo;
		if(empty($limit))  $sql_limit = " LIMIT ".$limit;
		$sql_order = ($order == 'ASC') ? ' ORDER BY id_ecart ASC ' : ' ORDER BY id_ecart DESC ' ;
		$sql = " 
			SELECT * 
			FROM  ecart_stock ".$sql_order.$sql_limit;

        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new EcartStock($value);
        }
        return $utable ;
    }
    
	public static function getEcartsStockBydate($date = FALSE) {
		global $pdo;
		 
		$day = ($date == FALSE) ? date('Y-m-d') : $date ;
		$sql_where =  ' date_ecart BETWEEN '.$date.' 00:00:00  AND '.$date.' 23:59:59 ' ;

        $sql = "SELECT * FROM ecart_stock WHERE ".$sql_where."  ORDER BY id_ecart DESC";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0) return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new EcartStock($value);
        }

        return $utable;
	}

    public static function getEcartStocksByRef($reference) {
        global $pdo;

        $sql = " SELECT * FROM ecart_stock "
                . " WHERE reference '=" . $reference . "'   ORDER BY id_ecart DESC ";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new EcartStock($value);
        }

        return $utable;
    }
	
}
?>