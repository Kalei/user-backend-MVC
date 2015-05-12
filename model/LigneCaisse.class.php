<?php

/**
 * Description of Ligne Caisse
 *
 * @author Hugo Rovelli
 */
class LigneCaisse {

     private $data;
     private $id;

     public function __construct($row, $new = false) {
          $i = 0;

          foreach ($row as $key => $value) {
               //On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
               if ($i == 0 && $value != null && $new == false) {
                    $this->id = $value;
               }
               $this->$key = $value;
               $i++;
          }
     }

     function stripslashes_array($value) {
          $value = is_array($value) ? array_map('stripslashes_array', $value) : stripslashes($value);
          return $value;
     }

     public function save() {
          global $pdo;

          if (!empty($this->id)) {
               $sql = " UPDATE lignecaisse SET ";
               $set = array();

               foreach ($this->data as $att => $value) {
                    $strip_value = $this->stripslashes_array($value);
                    if ($att != 'id' && $att != 'id_lignecaisse' && $att != 'id_caisse' && isset($value)) {
                         $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                    }
               }

               $sql .= implode(",", $set);
               $sql .= " WHERE id_lignecaisse =" . $this->id;

               $last_id = $this->id;
               try {
                    //echo $sql;
                    $pdo->exec($sql);
               } catch (PDOException $e) {
                    echo $e;
               }
          } else {

               //Insertion d'un nouvel élément
               $sql = " INSERT INTO lignecaisse ";
               $sql .= "(" . implode(",", array_keys($this->data)) . ") ";
               foreach (array_values($this->data) as $value) {
                    $values[] = $pdo->quote($value);
               }
               $sql .= "values (" . implode(",", array_values($values)) . ")";

               //echo $sql;

               try {
                    $pdo->exec($sql);
               } catch (PDOException $e) {
                    echo "Erreur d'insert : " . $e;
               }


               //On réccupère son id d'enregistrement (Last insert id)
               $query = "SELECT id_lignecaisse FROM lignecaisse ORDER BY id_lignecaisse DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               //var_dump($res);

               $last_id = $res[0]["id_lignecaisse"];
          }

          return $last_id == false ? NULL : $last_id;
     }

     public function __get($prop) {
        return htmlspecialchars($this->data[$prop]);
     }
	 public function __set($prop, $value) {
          $this->data[$prop] = $value;
     }


	public function setPercentRemise($remise) {  // Calcul par attribution
		$this->montant_remise_unitaire_ht = $this->prix_public_ht * ($remise/100) ;
		$this->montant_unitaire_ht = $this->prix_public_ht - $this->montant_remise_unitaire_ht;
	}
	public function setMontantRemiseUnitaireHT($remise, $mode = 'ht') { // Calcul par attribution
		if(intval($remise)>=0) {
			if($mode == 'ttc')  {
				$article = articleTable::getArticleByRef($this->reference);
				$this->montant_remise_unitaire_ht =  $remise / (($article->tva+100)/100);
			}
			else $this->montant_remise_unitaire_ht =  $remise ;
			$this->montant_unitaire_ht = $this->prix_public_ht - $this->montant_remise_unitaire_ht;
		}
	}
    public function getPrixPublicHTFromArticle() {
          $article = articleTable::getArticleByRef($this->reference);
          $taux_tva = (100 + $article->tva)/100;
          return $article->prix_public /$taux_tva;
     }
	 public function getPrixPublicTTCFromArticle() {
          $article = articleTable::getArticleByRef($this->reference);
          $taux_tva = (100 + $article->tva)/100;
          return $article->prix_public /$taux_tva;
     }
	 public function getMontantUnitaireHT() {
          $article = articleTable::getArticleByRef($this->reference);
          $taux_tva = (100 + $article->tva)/100;
          return ($article->prix_public /$taux_tva)-$this->montant_remise_unitaire_ht;
     }
	 public function getMontantRemiseUnitaireHT() {
          return $this->montant_remise_unitaire_ht ;
     }
	 public function getMontantRemiseUnitaireTTC() {
          $article = articleTable::getArticleByRef($this->reference);
          $taux_tva = (100 + $article->tva)/100;
          return $this->montant_remise_unitaire_ht *$taux_tva;
     }
	 public function getPercentRemise() {
		   return round(($this->montant_remise_unitaire_ht/$this->prix_public_ht)*100);
     }

     public function setQuantite($quantite) {
          $this->quantite = $quantite;
     }

     public function getQuantite() {
          return $this->quantite;
     }

}

?>