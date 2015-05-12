<?php

/**
 * Description of articleTampon
 *
 * @author Jean-Loup
 */
class articleTamponTable {

	public static function getArticleTamponById($id_tampon) {
		global $pdo;
	
		$sql = "SELECT * FROM article_tampon WHERE id_tampon='" . $id_tampon. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new ArticleTampon($value);
		}
		return $utable[0];
	}
	
	public static function getArticleTamponByEAN($code_EAN, $return_array = FALSE) {
		global $pdo;
		
		$sql = "SELECT * FROM article_tampon WHERE code_EAN='" . $code_EAN. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Article($value);
		}

		if($return_array == FALSE)  return $utable[0];
		else return  $utable;
     }
	
	public static function getArticleTamponByCF($code_fournisseur, $return_array = FALSE) {
		global $pdo;
	
		$sql = "SELECT * FROM article_tampon WHERE code_fournisseur ='" . $code_fournisseur. "' ";
		//echo $sql;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		 if (count($res) == 0)
			return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
			$utable[$key] = new Article($value);
		}

		if($return_array == FALSE)  return $utable[0];
		else return  $utable;
     } 
}
?>