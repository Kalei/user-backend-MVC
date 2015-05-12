<?php

/**
 * Objet panier.
 * 
 * V1 @author Hugo Rovelli  => sept.2013
  v2 @author Jean-Loup Couegnas => Juillet 2014
 */
class Caisse_2 {

// private implique que je ne peux acceder à ses variables qu'à travers l'une des fonctions de l'objet (GETTER et SETTER)
     private $id; // $row['id_caisse'] est forcement une string , $id sera un entier
     private $lignes_caisse;
     private $nb_references;
     private $nb_items;
     private $total_pvc;
     private $total_prix_public;
     private $total_caisse;
     public $id_caisse;
     public $id_client;
     public $id_user;
     //public $id_modepaiement;
     private $percent_remise;
     public $montant_remise_ht;
     private $data;
     public $montant_tva10;
     public $montant_tva20;
     public $info_remise;
     public $montant_tva;
     public $id_statut_caisse;
     public $exoneration_tva;
     public $close;

// __construct () sert à initialiser les valeurs des variables de l'objet
     public function __construct($row = null) {
          // Valeurs par défaut
          $this->id = 0;
          $this->id_caisse = 0;
          $this->lignes_caisse = array();
          $this->id_statut_caisse = 51;
          $this->close = 0;
          $this->nb_references = 0;
          $this->nb_items = 0;
          $this->total_pvc = 0;
          $this->total_prix_public = 0;
          $this->total_caisse = 0;
          $this->percent_remise = 0;
          $this->montant_remise_ht = 0;
          $this->id_client = 1;
          $this->id_user = 1;
          $this->data = array();
          $this->montant_tva10 = 0;
          $this->montant_tva20 = 0;
          $this->montant_tva = 0;
          $this->exoneration_tva = 0;
          $this->info_remise = '';

          // Valeurs obtenus par $row
          if ($row != null) {
               $i = 0;
               foreach ($row as $key => $value) {
                    //On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
                    if ($i == 0 && $value != null) { //&& $new == false
                         $this->id_caisse = $value;
                    }
                    $this->$key = $value;
                    $i++;
               }
          }

          $this->id = intval($this->id_caisse);

          $this->setLignesCaisse();
          $this->setCaisseQuantite();
          $this->setTotalPrixPublic();
     }

// private function => fonction réservée à l'objet

     private function setTotalPrixPublic() {
          $this->total_pvc = 0;
          $this->total_prix_public = 0;
          foreach ($this->lignes_caisse as $ligne) {
               $article = articleTable::getArticleByRef($ligne->reference);
               $this->total_pvc += $article->getPvc() * $ligne->quantite;
               $this->total_prix_public += $article->getPrixPublic() * $ligne->quantite;
          }
          $this->total_caisse = ($this->total_prix_public - $this->getMontantRemise()) - ($this->total_prix_public * $this->percent_remise);
     }

     public function setTotalCaisse() {
          $this->setTotalPrixPublic();
          $this->total_caisse = ($this->total_prix_public ) - ($this->total_prix_public * $this->percent_remise) - $this->getMontantRemise();
     }

// Fonctions relatives au mise à jour du Caisse (ajouter, suppression d'articles)
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
     public function setCaisseQuantite() { // Test la référence existe déjà dans le panier
          $this->nb_items = 0;
          $this->nb_references = 0;
          foreach ($this->lignes_caisse as $ligne) {
               $this->nb_items += $ligne->getQuantite();
               $this->nb_references += 1;
          }
     }

     public function existLignes($refTest) { // Test la référence existe déjà dans le panier
          if (empty($refTest))
               return false;
          else {
               foreach ($this->lignes_caisse as $refExist)
                    if ($refTest->reference == $refExist->reference)
                         return $refExist;
               return false;
          }
     }

     public function setLigneQte($key_ligne, $nb = 1) {

          if (key_exists($key_ligne, $this->lignes_caisse)) {
               $ligne_actuelle = $this->lignes_caisse[$key_ligne];
               $addedArticle = articleTable::getArticleByRef($ligne_actuelle->reference);
               $key_exists = $this->refExistsExists($ligne_actuelle->reference);

               if (key_exists(21, $key_exists)) {
                    $ligne_actuelle = $this->lignes_caisse[$key_exists[21]];
               }

               if ($ligne_actuelle->id_etat_ligne_commande == 52) {
                    if ($addedArticle->stock < $nb) {
                         $nb_commander = $nb - $addedArticle->stock;

                         $ligne = array("id_caisse" => $this->getCaisseId(), "reference" => $addedArticle->reference, "quantite" => $nb_commander, "id_etat_ligne_commande" => 21);
                         $ligneAdd = new LigneCaisse_2($ligne);
                         array_push($this->lignes_caisse, $ligneAdd);

                         $nb_delivrable = $nb - $nb_commander;
                         $ligne_actuelle->setQuantite($nb_delivrable); // J'ajoute la quantité à la quantité initiale
                    } else {
                         $ligne_actuelle->setQuantite($nb); // J'ajoute la quantité à la quantité initiale
                    }
               } else {
                    $ligne_actuelle->setQuantite($nb); // J'ajoute la quantité à la quantité initiale
               }

               $this->setCaisseQuantite();
               $this->setTotalPrixPublic();
               $this->setTVA();

               return true;
          }

          return false;
     }

     public function addLigne($refAdd) {
          if (!empty($refAdd)) {

               $addedArticle = articleTable::getArticleByRef($refAdd);
               $key_exists = $this->refExistsExists($refAdd);

               if (count($key_exists) == 0) {
                    if ($addedArticle->stock < 1) {
                         $ligne = array("id_caisse" => $this->getCaisseId(), "reference" => $refAdd, "quantite" => 1, "id_etat_ligne_commande" => 21);
                         $ligneAdd = new LigneCaisse_2($ligne);
                         array_push($this->lignes_caisse, $ligneAdd);
                    } else {
                         $ligne = array("id_caisse" => $this->getCaisseId(), "reference" => $refAdd, "quantite" => 1, "id_etat_ligne_commande" => 52);
                         $ligneAdd = new LigneCaisse_2($ligne);
                         array_push($this->lignes_caisse, $ligneAdd);
                    }

                    $this->setCaisseQuantite();
                    $this->setTotalPrixPublic();
                    $this->setTVA();
               } else {
                    $key_modif = (key_exists(21, $key_exists)) ? $key_exists[21] : $key_exists[52];
                    $nb = $this->lignes_caisse[$key_modif]->getQuantite() + 1;
                    $this->setLigneQte($key_modif, $nb);
               }
               return true;
          }
          return false;
     }

     public function refExistsExists($ref) {
          $keys = array();

          foreach ($this->lignes_caisse as $key => $ligne) {
               if ($ligne->reference == $ref) {
                    $keys[$ligne->id_etat_ligne_commande] = $key;
               }
          }

          return $keys;
     }

     public function delLigneByRef($del_key) {

          if (key_exists($del_key, $this->lignes_caisse)) {
               unset($this->lignes_caisse[$del_key]);

               $this->setCaisseQuantite();
               $this->setTotalPrixPublic();
               $this->setTVA();

               return true;
          }

          return false;
     }

     public function changeStatutLigneByRef($change_ligne, $id_etat_ligne_commande) {
          if ($change_ligne != NULL && $id_etat_ligne_commande != NULL) {
               $this->lignes_caisse[$change_ligne]->id_etat_ligne_commande = $id_etat_ligne_commande;
          }
     }

     public function setTVA() {
          $this->montant_tva = 0;

          if ($this->exoneration_tva == 0) {
               $this->setTVA10();
               $this->setTVA20();
               $this->montant_tva = $this->montant_tva10 + $this->montant_tva20;
          }
     }

     public function setTVA10() {
          $this->montant_tva10 = 0; // On réinitialise le montant de la TVA à 10	
          if (!empty($this->lignes_caisse)) { // S 'il y a des lignes
               foreach ($this->lignes_caisse as $ligne) {
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 10) {
                         $this->montant_tva10 += $ligne->getMontantLigneHt() * (0.1);
                    }
               }
               $this->montant_tva10 = round($this->montant_tva10, 2);
          }
     }

     public function setTVA20() {
          $this->montant_tva20 = 0; // On réinitialise le montant de la TVA à 20	
          if (!empty($this->lignes_caisse)) { // S'il y a des lignes
               foreach ($this->lignes_caisse as $ligne) {
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 20)
                         $this->montant_tva20 += $ligne->getMontantLigneHt() * (0.2);
               }
               $this->montant_tva20 = round($this->montant_tva20, 2);
          }
     }

     public function setPercentRemise($remise) {
          $this->percent_remise = $remise / 100;
          $this->setCaisseQuantite();
          $this->setTotalPrixPublic();
          $this->setTVA();
     }

     public function setMontantRemise($remise) {
          $this->montant_remise_ht = $remise;
          $this->setCaisseQuantite();
          $this->setTotalPrixPublic();
          $this->setTVA();
     }

     public function setLignesCaisse() {
          // Cas d'une caisse déjà en bdd ou des lignes sont déjà existantes // on remplace le contenu des lignes de caisse en cours par celles existantes
          $lignes_caisse_bdd = ligneCaisseTable_2::getLignesByIdCaisse($this->getCaisseId());
          //echo '<br><br><pre>';
          //var_dump($lignes_caisse_bdd);
          //echo '<br><br></pre>';
          if ($lignes_caisse_bdd == TRUE) {
               return $this->lignes_caisse = $lignes_caisse_bdd;
          }
          $this->setTotalPrixPublic();
     }

     public function save($update = FALSE) {
          global $pdo;

          if ($update == TRUE) {
               if (caisseTable::getCaisseById($this->id_caisse) == TRUE) { // Si la caisse existe déjà en base de données	
                    $sql = "UPDATE caisse_2 SET ";
                    $set = array();

                    foreach ($this->data as $att => $value) {
                         $strip_value = $this->stripslashes_array($value);
                         if ($att != 'id' && $att != 'id_caisse' && $att != 'lignes_caisse' && isset($value)) {
                              $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                         }
                    }

                    $sql .= implode(",", $set);
                    $sql .= " WHERE id_caisse =" . $this->id;

                    $last_id = $this->id;
                    try {
                         //echo $sql;
                         $pdo->exec($sql);
                    } catch (PDOException $e) {
                         echo $e;
                    }
               } else
                    echo "Vous essayer de mettre à jour une caisse qui n'est pas (ou plus) repertoriée dans la base de données";
          }
          else { //Insertion d'un nouvel élément
               $this->data = array("id_client" => $this->id_client, "id_user" => $this->id_user,
                   "montant_remise_ht" => $this->montant_remise_ht + ($this->total_prix_public * $this->percent_remise), "info_remise" => $this->info_remise, "montant_vente_ht" => $this->getTotalCaisse(),
                   "montant_tva" => $this->montant_tva, "montant_tva10" => $this->montant_tva, "montant_tva20" => $this->montant_tva10, "close" => $this->close);

               $sql = " INSERT INTO caisse_2 ";
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
               $query = "SELECT id_caisse FROM caisse_2 ORDER BY id_caisse DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               $last_id = $res[0]["id_caisse"];
               $this->id_caisse = $last_id;
               $this->id = intval($last_id);

               foreach ($this->lignes_caisse as $ligne) {
                    $values = array("id_caisse" => $last_id, "reference" => $ligne->reference, "prix_public_ht" => $ligne->getMontantLigneHt(), "montant_unitaire_ht" => $ligne->getMontantLigneHt() / $ligne->getQuantite(), "quantite" => $ligne->getQuantite(), "id_statut_ligne_caisse" => $ligne->id_statut_lignecaisse);
                    $current_ligne = new LigneCaisse_2($values, true);
                    $current_ligne->save();
               }
          }

          return ($last_id == false) ? NULL : $last_id;
     }

     public function getCaisseId() {
          return $this->id;
     }

     public function getLignesCaisse() {
          $this->setLignesCaisse();
          return $this->lignes_caisse;
     }

     public function getNbItems() {
          $this->setCaisseQuantite();
          return $this->nb_items;
     }

     public function getClient() {
          if ($this->id_client > 1) {
               return clientTable::getClientById($this->id_client);
          } else
               return null;
     }

     public function getNbReferences() {
          return $this->nb_references;
     }

     public function isPaiementAchived($list_paiement) {
          $total_paiement = 0;

          foreach ($list_paiement as $paiement) {
               $total_paiement+=$paiement->montant_paiement;
          }

          if (number_format(floatval($total_paiement), 2) == number_format(floatval($this->getTotalCaisse()), 2)) {
               return 0;
          }

          $diff = number_format(floatval($this->getTotalCaisse()), 2) - number_format(floatval($total_paiement), 2);
          return $diff;
     }

     public function getTotalPrixPublic() {
          return $this->total_prix_public;
     }

     public function getTotalCaisse() {
          return $this->total_prix_public - ($this->total_prix_public * $this->percent_remise) - $this->montant_remise_ht;
     }

     public function getMontantRemise() {
          return $this->montant_remise_ht;
     }

     public function getPercentRemise() {
          return $this->percent_remise;
     }

     public function getMontantTVA() {
          $this->setTVA();
          return $this->montant_tva;
     }

     function stripslashes_array($value) {
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