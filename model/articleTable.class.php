<?php

/**
 * Description of article
 *
 * @author Hugo Rovelli
 */
class articleTable {

     // Méthode => récupère un objet article contenant les données SQL à partir de la référence
     public static function getArticleByRef($reference) {
          global $pdo;

         $sql = "SELECT * FROM article WHERE reference='" . $reference . "' AND prix_public>0";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if (count($res) == 0)
               return false;
			//var_dump($res[0]);
          return new Article($res[0]);
     }
	 

     // Méthode => récupère un objet article contenant les données SQL à partir de la référence
     public static function getArticleByEAN($code_ean, $return_array = FALSE) {
          global $pdo;

          $sql = "SELECT * FROM article WHERE code_EAN='" . $code_ean . "' AND prix_public>0";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }

          if ($return_array == FALSE)
               return $utable[0];
          else
               return $utable;
     }

     // Méthode => récupère un objet article contenant les données SQL à partir de son nom de modèle
     public static function getArticleByModel($model, $return_array = FALSE) {
          global $pdo;

          $sql = "SELECT * FROM article WHERE modele='%" . $model . "%';";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }

          if ($return_array == FALSE)
               return $utable[0];
          else
               return $utable;
     }

     public static function getArticleByCF($code_fournisseur, $return_array = FALSE) {
          global $pdo;

          $sql = "SELECT * FROM article WHERE code_fournisseur='" . $code_fournisseur . "' ORDER BY modele ASC;";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);


          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }

          if ($return_array == FALSE)
               return $utable[0];
          else
               return $utable;
     }

     // Méthode accessoire => récupère un tableau d'objets articles
     public static function getArticles($limit=FALSE) {
          global $pdo;
		  
		  $sql_limit = ($limit==FALSE) ? '' : $limit;

          echo $sql = "SELECT * FROM article ORDER BY reference DESC ".$sql_limit;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

         foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }
		  return $utable;

     }
	  // Méthode accessoire => récupère un tableau d'objets articles
     public static function getArticlesSoldes($limit=FALSE) {
          global $pdo;
		  
		  $sql_limit = ($limit==FALSE) ? '' : $limit;

          $sql = "SELECT * FROM article WHERE soldes =1 ORDER BY reference DESC ".$sql_limit;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

         foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }
		  return $utable;

     }
	 // Méthode accessoire => récupère un tableau d'objets articles
     public static function getArticlesStockTheoriqueNegatif($limit=FALSE) {
          global $pdo;
		  
		  $sql_limit = ($limit==FALSE) ? '' : $limit;

          $sql = "SELECT * FROM article WHERE stock_theorique<0 ORDER BY reference DESC ".$sql_limit;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

         foreach ($res as $key => $value) {
               $utable[$key] = new Article($value);
          }
		  return $utable;

     }

     public static function getArticleByProduitName($model) {
          global $pdo;

          $sql = "SELECT article.*, produit.nom as 'nom_produit' FROM produit JOIN article ON article.id_produit=produit.id_produit WHERE produit.nom LIKE '" . $model . "%' LIMIT 20";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		  if (count($res) == 0)
               return false;
          
		  foreach ($res as $key => $value) {
               $atable[$key] = new Article($value);
          }

          return $atable;
     }
	 public static function getArticlesStockNegatif() {
          global $pdo;

          $sql = "SELECT * FROM article WHERE stock<0 ";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		  if (count($res) == 0)
               return false;
          
		  foreach ($res as $key => $value) {
               $atable[$key] = new Article($value);
          }

          return $atable;
     }
}

?>
