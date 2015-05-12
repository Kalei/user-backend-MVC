<?php

/**

  v1.2 @author Jean-Loup Couegnas => Février 2015
  d'après l'objet Caisse et Panier d'Hugo Rovelli
 */
class Facture {

// private implique que je ne peux acceder à ses variables qu'à travers l'une des fonctions de l'objet (GETTER et SETTER)
     private $id; // $row['id_facture'] est forcement une string , $id sera un entier
     private $data;
     private $lignes_facture;
     public $id_facture;
     public $id_client;
     public $source;
     public $id_source;
     private $montant_ht;
     private $montant_fdp_ht;
     private $montant_remise_ht;
     public $montant_tva10;
     public $montant_tva20;
     public $montant_tva;
     public $accompte;
     public $date_facture;
     public $date_echeance;
     public $exoneration_tva;
     public $notes;
     public $close;

// __construct () sert à initialiser les valeurs des variables de l'objet
     public function __construct($row = null) {
          //var_dump($row);
          // Valeurs par défaut
          $this->id = 0;
          $this->close = 0;
          $this->notes = '';
          $this->id_facture = 0;
          $this->id_client = 1;
          $this->lignes_facture = array();
          $this->source = 0;
          $this->id_source = 0;
          $this->accompte = 0;
          $this->montant_ht = 0;
          $this->montant_fdp_ht = 0;
          $this->montant_remise_ht = 0;
          $this->montant_tva10 = 0;
          $this->montant_tva20 = 0;
          $this->montant_tva = 0;
          $this->exoneration_tva = 0;
		  $this->data = $row;

          // Valeurs obtenus par $row
          if ($row != null) {
               $i = 0;
               foreach ($row as $key => $value) {
                    //On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
                    if ($i == 0 && $value != null) { //&& $new == false
                         $this->id_facture = $value;
                    }
                    $this->$key = $value;
                    $i++;
               }
          }
          $this->id = intval($this->id_facture);
          $this->setLignesFacture();
     }

// private function => fonction réservée à l'objet

     private function setMontantHT() {
          $this->montant_ht = 0;
          if (!empty($this->lignes_facture)) {
               foreach ($this->lignes_facture as $ligne)
                    $this->montant_ht += $ligne->montant_unitaire_ht * $ligne->quantite;
          }
     }

     public function setMontantRemiseHT($remise_ht = FALSE) {
          if ($remise_ht === FALSE) {
               $this->montant_remise_ht = 0;
               if (!empty($this->lignes_facture)) {
                    foreach ($this->lignes_facture as $ligne) {
                         $this->montant_remise_ht += $ligne->remise_ht * $ligne->quantite;
                    }
               }
          } else
               $this->montant_remise_ht = $remise_ht;
     }

     public function existLignes($refTest) { // Test la référence existe déjà dans le panier
          if (empty($refTest))
               return false;
          else {
               foreach ($this->lignes_facture as $refExist)
                    if ($refTest->reference == $refExist->reference)
                         return $refExist;
               return false;
          }
     }

     public function addLigne($refAdd, $nb = 1, $reset_qte = FALSE, $ppht, $remise_ht = 0) {
          if (!empty($refAdd)) {
               $exist = false;

               // Test la référence existe déjà dans le panier
               if ($this->getLignesFacture() != FALSE) {
                    foreach ($this->getLignesFacture() as $refExist) {
                         // echo $refExist->reference. ' # '.$refExist->quantite.'<br>';
                         if ($refAdd == $refExist->reference) {
                              if ($reset_qte == TRUE)
                                   $refExist->setQuantite($nb); // J'ajoute la quantité à la quantité initiale
                              else
                                   $refExist->setQuantite($refExist->getQuantite() + $nb); // J'ajoute la quantité à la quantité initiale
                              $exist = true;
                         }
                    }
               }
               // C'est une référence qui n'est pas encore dans le panier
               if ($exist == false) {
                    $ligne = array(
                        "id_facture" => $this->getFactureId(), "reference" => $refAdd, "quantite" => $nb,
                        "prix_public_ht" => $ppht, "remise_ht" => $remise_ht, "montant_unitaire_ht" => ($ppht - $remise_ht)
                    );
                    $ligneAdd = new LigneFacture($ligne);
                    array_push($this->lignes_facture, $ligneAdd);
                    //$refAdd->setQuantite($nb);
               }
               $this->setMontantRemiseHT();
               $this->setMontantHT();
               $this->setTVA();
          }
          return false;
     }

     public function delLigneByRef($del_ligne) {
          if ($del_ligne != NULL && $del_ligne != "") {
               foreach ($this->lignes_facture as $key => $ligne) {
                    if ($del_ligne == $ligne->reference) {
                         unset($this->lignes_facture[$key]);
                    }
               }
          }
          $this->setMontantHT();
          $this->setTVA();
     }

// Fonctions relatives à la TVA
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••	


     public function setTVA() {
          $this->montant_tva = 0;
          if ($this->exoneration_tva == 0) {
               $this->setTVA10();
               $this->setTVA20();
               $this->montant_tva = $this->montant_tva10 + $this->montant_tva20 + ($this->montant_fdp_ht * 0.2);
          }
     }

     public function setTVA10() {
          $this->montant_tva10 = 0; // On réinitialise le montant de la TVA à 10	
          if (!empty($this->lignes_facture)) { // S 'il y a des lignes
               foreach ($this->lignes_facture as $ligne) {
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 10)
                         $this->montant_tva10 += $ligne->getMontantLigneHT() * (0.1) * $ligne->quantite;
               }
               $this->montant_tva10 = round($this->montant_tva10, 2);
          }
     }

     public function setTVA20() {
          $this->montant_tva20 = 0; // On réinitialise le montant de la TVA à 20	
          if (!empty($this->lignes_facture)) { // S'il y a des lignes
               foreach ($this->lignes_facture as $ligne) {
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 20)
                         $this->montant_tva20 += $ligne->getMontantLigneHT() * (0.2) * $ligne->quantite;
               }
               $this->montant_tva20 = round($this->montant_tva20, 2);
          }
     }

     public function setLignesFacture() {
          // Cas d'une facture déjà en bdd ou des lignes sont déjà existantes
          // on remplace le contenu des lignes de facture en cours par celles existantes
          $lignes_facture_bdd = ligneFactureTable::getLignesByIdFacture($this->getFactureId());
          //echo '<br><br><pre>';
          //var_dump($lignes_facture_bdd);
          //echo '<br><br></pre>';

          if ($lignes_facture_bdd == TRUE)
               return $this->lignes_facture = $lignes_facture_bdd;

          else if (count($this->lignes_facture) > 0)
               $this->lignes_facture = $this->lignes_facture;

          $this->setMontantRemiseHT();
          $this->setMontantHT();
          $this->setTVA();
     }

     public function save($update = FALSE) {
          global $pdo;
          $this->data['notes'] = $this->notes;
          $this->data['close'] = $this->close;
          if ($update == TRUE) {
               if (factureTable::getFactureById($this->getFactureId()) == TRUE) { // Si la facture existe déjà en base de données	
                    $sql = "UPDATE facture SET ";
                    $set = array();

                    foreach ($this->data as $att => $value) {
                         $strip_value = $this->stripslashes_array($value);
                         if ($att != 'id' && $att != 'id_facture' && $att != 'lignes_facture' && isset($value)) {
                              $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                         }
                    }

                    $sql .= implode(",", $set);
                    $sql .= " WHERE id_facture=" . $this->id_facture;

                    //echo $sql;

                    $last_id = $this->id;
                    try {
                         $pdo->exec($sql);
                    } catch (PDOException $e) {
                         echo $e;
                    }
               } else
                    echo "Vous essayer de mettre à jour une facture qui n'est pas (ou plus) repertoriée dans la base de données";
          }
          else { //Insertion d'un nouvel élément
               $montant_ht = (!empty($this->montant_ht)) ? $this->montant_ht : $this->getMontantHT();
               $this->data = array("id_client" => $this->id_client,
                   "source" => $this->source, "id_source" => $this->id_source,
                   "montant_remise_ht" => $this->montant_remise_ht, "montant_ht" => $montant_ht, "montant_fdp_ht" => $this->getMontantFdpHT(),
                   "accompte" => $this->accompte, "date_facture" => $this->date_facture, "date_echeance" => $this->date_echeance,
                   "exoneration_tva" => $this->exoneration_tva, "montant_tva" => $this->montant_tva, "notes" => $this->notes, "close" => $this->close, "montant_tva10" => $this->montant_tva10, "montant_tva20" => $this->montant_tva20);
               $sql = " INSERT INTO facture ";
               $sql .= "(" . implode(",", array_keys($this->data)) . ") ";
               foreach (array_values($this->data) as $value) {
                    $values[] = $pdo->quote($value);
               }
               $sql .= "values (" . implode(",", array_values($values)) . ")";

               try {
                    //echo $sql;
                    $pdo->exec($sql);
               } catch (PDOException $e) {
                    echo "Erreur d'insert : " . $e;
               }
               // R2cupérer l'i
               //On réccupère son id d'enregistrement (Last insert id)
               $query = "SELECT id_facture FROM facture ORDER BY id_facture DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               $last_id = $res[0]["id_facture"];
               $this->id_facture = $last_id;

               foreach ($this->lignes_facture as $ligne) {
                    $ligne->id_facture = $this->id_facture;
                    $ligne->save();
               }
          }

          return ($last_id == false) ? NULL : $last_id;
     }

     public function getFactureId() {
          return $this->id_facture;
     }

     public function getLignesFacture() {
          $this->setLignesFacture();
          return $this->lignes_facture;
     }

     public function getClient() {
          if ($this->id_client > 1) {
               return clientTable::getClientById($this->id_client);
          } else
               return null;
     }

     public function getMontantHT() {
          $this->setMontantHT();
          return round($this->montant_ht, 2);
     }

     public function getMontantFdpHT() {
          return round($this->montant_fdp_ht, 2);
     }

     public function getTotalFactureTTC() {
          $total = $this->montant_ht + $this->montant_fdp_ht + $this->montant_tva;
          return round($total, 2);
     }

     public function getTotalFactureHT() {
          $total = $this->montant_ht + $this->montant_fdp_ht;
          return round($total, 2);
     }

     public function getMontantRemiseHT() {
          return $this->montant_remise_ht;
     }

     public function getMontantTVA() {
          return $this->montant_tva;
     }

     public function getMontantTVA10() {
          return $this->montant_tva10;
     }

     public function getMontantTVA20() {
          return $this->montant_tva20;
     }

     public function stripslashes_array($value) {
          $value = is_array($value) ? array_map('stripslashes_array', $value) : stripslashes($value);
          return $value;
     }

     public function __get($prop) {
          return htmlspecialchars($this->data[$prop]);
     }

     public function __set($prop, $value) {
          $this->data[$prop] = $value;
     }

}

?>