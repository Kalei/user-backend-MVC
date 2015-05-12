<?php

/**
 * Description of commandeCaisseTable
 *
 * @author Jean-Loup Couegnas
 */
class commandeCaisseTable {

     public static function getCommandeCaisseById($id) {
          global $pdo;

          $sql = "SELECT * FROM commande_caisse WHERE id_caisse=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new CommandeCaisse($res[0]);
     }

	public static function getCommandeCaisses() {
		global $pdo;
		
		echo $sql = "SELECT * FROM commande_caisse ORDER BY id_caisse ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0) return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new CommandeCaisse($value);
		}
		return $utable;
	}

}
?>