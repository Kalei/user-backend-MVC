<?php
/**
 * Objet article contenant toutes les propriétés de la table article
 *
 * @author Hugo Rovelli
 */
class Article {

     //Promotion globale si soldes est à 1 dans la table.
     // La valeur article.soldes sert à identifié les articles qui compatibles avec les codes promos
     //Todo: Chercher un moyen de modifier la valeur par l'admin
     //
    
    ///////////////////////////////////////////////////////////


     private $data;
     private $id;
     protected $primary_key;
     private $pvc; // Prix de vente conseillé unitaire
     private $prix_public_ht ;
     private $prix_vente ; // Prix de vente unitaire
     public $taux_marge;

     /**
      * Constructeur article
      * 
      * @param type $row Résultat requète.
      */
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

          $this->primary_key = $this->id;
          $this->prix_vente = $this->prix_public;

          if ($this->prix_nonpromo > $this->prix_public) {
               $this->pvc = $this->prix_nonpromo ;
               $this->prix_vente = $this->prix_public ;
          } else {
               $this->pvc = $this->prix_public;
               $this->prix_vente = $this->prix_public;
          }
          $this->taux_marge = ($this->prix_achat == 0 || $this->prix_public == 0) ? (0.4) : ($this->prix_public - $this->prix_achat) / $this->prix_public;
          //echo $this->reference.'='.$this->stock.'='.$this->stock_theorique.'<br>';
		  $this->setStockTheorique();
     }

     function stripslashes_array($value) {
          $value = is_array($value) ? array_map('stripslashes_array', $value) : stripslashes($value);
          return $value;
     }

     public function save() {
          global $pdo;

          if (!empty($this->id)) {
               $sql = " UPDATE article SET ";
               $set = array();
               foreach ($this->data as $att => $value) {
				   //if($att == 'stock_theorique') echo $att.' : '.$value;
                    $strip_value = $this->stripslashes_array($value);
                    if ($att != 'id' && $att != 'reference' && isset($value)) {
                         $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                    }
               }

               $sql .= implode(",", $set);
               $sql .= " WHERE reference =" . $this->reference;

               $last_id = $this->id;
               try {
				   //echo $sql ;
               		$pdo->exec($sql);
               } catch (PDOException $e) {
                    echo $e;
               }
          } else {

               //Insertion d'un nouvel élément
               $sql = " INSERT INTO article ";
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
               $query = "SELECT reference FROM article ORDER BY reference DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               //var_dump($res);

               $last_id = $res[0]["reference"];
          }

          return $last_id == false ? NULL : $last_id;
     }

     public function __get($prop) {
          return htmlspecialchars($this->data[$prop]);
     }

     public function __set($prop, $value) {
          $this->data[$prop] = $value;
     }



     public function setStockTheorique() {

          // Lignes en commande Fournisseur Actives
          $lignecde_fournisseur_receiveable = fournisseurLigneCommandeTable::getNbLigneReceiveable($this->reference);
		  //echo '<br>Lignes en commande Fournisseur Actives : '. $lignecde_fournisseur_receiveable ;
          
		  // Lignes en commande Client Actives
          $lignecde_internet_delivereable = ligneCommandeTable::getNbLigneDelivereable($this->reference);
          $lignecde_caisse_delivereable = ligneCaisseTable::getNbLigneDelivereableByRef($this->reference);
		  //echo '<br>Lignes en commande Client Actives : '. $lignecde_internet_delivereable ;

          $this->stock_theorique = ($this->stock - ($lignecde_internet_delivereable + $lignecde_caisse_delivereable)) + $lignecde_fournisseur_receiveable;
          return TRUE;
     }

     public function getPrixVente() {
          return $this->prix_vente;
     }

     public function getPrixPublic() {
          return $this->prix_public;
     }
	 public function getPrixPublicHT() {
		 $taux_tva = (100+$this->tva)/100 ;
         return round($this->prix_public/$taux_tva,2);
     }

     public function getPvc() {
          return $this->pvc;
     }

}
?>
