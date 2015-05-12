<?php

/**
 * Objet CaisseFoire
 * 
 *
  v1 @author Hugo Rovelli  => Décembre 2014
  v1.2 @author Jean-Loup Couegnas => Janvier 2015
  v1.3 @author Hugo Rovelli => Février 2015 (en dév).
 */
class CaisseFoire {

// private implique que je ne peux acceder à ses variables qu'à travers l'une des fonctions de l'objet (GETTER et SETTER)
     private $id; // $row['id_caisse_foire'] est forcement une string , $id sera un entier
     private $lignes_caisse;
     private $nb_references;
     private $nb_items;
     public $id_caisse_foire;
     public $id_client;
     public $id_vendeur;
     //public $id_modepaiement;
     private $percent_remise;
     public $total_remise_ht;
     public $total_remise_ttc;
     public $total_prix_public_ht;
     public $total_prix_public_ttc;
     public $total_caisse_ht;
     public $total_caisse_ttc;
     public $total_tva10;
     public $total_tva20;
     public $total_tva;
     public $info_remise;
     public $id_statut_caisse;
     public $exoneration_tva;
     private $data;
     public $payee;
	 public $date_achat;

// __construct () sert à initialiser les valeurs des variables de l'objet
     public function __construct($row = null) {
          // Valeurs par défaut
          $this->id = 0;
          $this->id_caisse_foire = 0;
          $this->lignes_caisse = array();
          $this->id_statut_caisse = 51;
          $this->nb_references = 0;
          $this->nb_items = 0;
          $this->percent_remise = 0;
          $this->total_remise_ht = 0;
          $this->total_remise_ttc = 0;
          $this->total_prix_public_ht = 0;
          $this->total_prix_public_ttc = 0;
          $this->total_caisse_ht = 0;
          $this->total_caisse_ttc = 0;
          $this->id_client = 1;
          $this->id_vendeur = 1;
          $this->total_tva10 = 0;
          $this->total_tva20 = 0;
          $this->total_tva = 0;
          $this->exoneration_tva = 0;
          $this->info_remise = '';
          $this->payee = 0;
          $this->data = array();

          // Valeurs obtenus par $row
          if ($row != null) {
               $i = 0;
               foreach ($row as $key => $value) {
                    //On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
                    if ($i == 0 && $value != null) { //&& $new == false
                         $this->id_caisse_foire = $value;
                    }
                    $this->$key = $value;
                    $i++;
               }
          }

          $this->id = intval($this->id_caisse_foire);
          $this->setLignesCaisseFoire();
     }

// private function => fonction réservée à l'objet
     public function setTotaux() {

		// Réinitialisation des totaux
		 $this->total_remise_ttc = $this->total_remise_ht = $this->total_caisse_ht =  $this->total_caisse_ttc = $this->total_prix_public_ht = $this->total_prix_public_ttc = 0;
		
		if(count($this->lignes_caisse)>0) {
			// Calcul des totaux à partir des lignes à condition que la remise sur les lignes est été définit
			foreach ($this->lignes_caisse as $ligne) {
				if($ligne->id_etat_ligne_commande !=41) { // Ligne en commande puis annulée
					$article = articleTable::getArticleByRef($ligne->reference);
					$this->total_prix_public_ht += $ligne->prix_public_ht * $ligne->quantite;
					$this->total_prix_public_ttc += ((100+$article->tva)/100) * $ligne->prix_public_ht * $ligne->quantite ;
					$this->total_caisse_ht += $ligne->montant_unitaire_ht * $ligne->quantite ;
					$this->total_caisse_ttc += ((100+$article->tva)/100) * $ligne->montant_unitaire_ht * $ligne->quantite ;
					$this->total_remise_ht += ($ligne->prix_public_ht-$ligne->montant_unitaire_ht) * $ligne->quantite ;
					$this->total_remise_ttc += ((100+$article->tva)/100) * ($ligne->prix_public_ht-$ligne->montant_unitaire_ht) * $ligne->quantite ;
				}
			}
			$this->total_prix_public_ttc = round($this->total_prix_public_ttc,1);
			$this->total_prix_public_ht = round($this->total_prix_public_ht,2);
			$this->total_caisse_ttc = round($this->total_caisse_ttc,1);
			$this->total_caisse_ht = round($this->total_caisse_ht,2);
			$this->total_remise_ttc = round($this->total_remise_ttc,2);
			$this->total_remise_ht = round($this->total_remise_ht,2);
			// Calcul du total caisse à partir du PP et de la remise
			// $this->total_caisse_ht = $this->total_prix_public_ht - $this->getTotalRemiseHT();
			// $this->total_caisse_ttc = $this->total_prix_public_ttc - $this->getTotalRemiseTTC();

			if($this->exoneration_tva==1) $this->total_caisse_ttc = $this->total_caisse_ht ;
			
			$this->setTVA();
			$this->setCaisseFoireQuantite();
		}
     }

// Fonctions relatives au mise à jour du CaisseFoire (ajouter, suppression d'articles)
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
     public function setCaisseFoireQuantite() { // Test la référence existe déjà dans le panier
          $this->nb_items = 0;
          $this->nb_references = 0;
          foreach ($this->lignes_caisse as $ligne) {
               $this->nb_items += $ligne->getQuantite();
               $this->nb_references += 1;
          }
     }

     public function setLigneQte($key_ligne, $nb = 1) {

          // On vérifie que la clef pointe une ligne de caisse
          if (key_exists($key_ligne, $this->lignes_caisse)) {
               $ligne_actuelle = $this->lignes_caisse[$key_ligne];
               $addedArticle = articleTable::getArticleByRef($ligne_actuelle->reference);

               // Si c'est une ligne en cours de commande
               if ($ligne_actuelle->id_etat_ligne_commande == 21)
                    $ligne_actuelle->setQuantite($nb);

               // Si c'et une ligne qui devait être retiré
               if ($ligne_actuelle->id_etat_ligne_commande == 52) {

                    if ($addedArticle->stock >= $nb) // le stock absorbe nouvelle quantité totale
                         $ligne_actuelle->setQuantite($nb);

                    else { // Le stock est insuffisant, il faut dispatcher entre la quantité retirable et la quantité "à commander"
                         $ligne_actuelle->setQuantite($addedArticle->stock);
                         $nb_a_commander = $nb - $addedArticle->stock ;
						 $prix_public_ht = round($addedArticle->prix_public/((100+$addedArticle->tva)/100),2);
                         $nouvelle_ligne = array("id_caisse_foire" => $this->getCaisseFoireId(), "reference" => $addedArticle->reference, "quantite" => $nb_a_commander, "id_etat_ligne_commande" => 21,
						  "montant_remise_unitaire_ht" => 0, "montant_unitaire_ht" => $prix_public_ht, "prix_public_ht" =>$prix_public_ht);
                         $this->lignes_caisse[] = new LigneCaisseFoire($nouvelle_ligne);
                    }
               }
               $this->setTotaux();
               return true;
          }
          return false;
     }

     public function addLigne($refAdd) {
          if (!empty($refAdd)) {

               $addedArticle = articleTable::getArticleByRef($refAdd);
               if ($addedArticle != FALSE) {

                    $key_lignes_exists = $this->keyLignesExistByRef($refAdd);
					
					// Nouvelle Ligne
                    if (count($key_lignes_exists) == 0) {
                         $etat_nouvelle_ligne = ($addedArticle->stock < 1) ? 21 : 52;
						 $prix_public_ht = round($addedArticle->prix_public/((100+$addedArticle->tva)/100),2);
                         $nouvelle_ligne = array("id_caisse_foire" => $this->getCaisseFoireId(),"id_lignecaisse" => 0, "reference" => $refAdd, "quantite" => 1, "id_etat_ligne_commande" => $etat_nouvelle_ligne,
						 	 "montant_remise_unitaire_ht" => 0, "montant_unitaire_ht" => $prix_public_ht, "prix_public_ht" =>$prix_public_ht);
                         $this->lignes_caisse[] = new LigneCaisseFoire($nouvelle_ligne);
                         $this->setTotaux();
                    } else {
                         // Par défaut on prend la ligne en cours de commande car si une ligne est en commande c'est qu'on a déjà vérifié qu'il n'y avait plus de stock
                         $key_modif = (key_exists(21, $key_lignes_exists)) ? $key_lignes_exists[21] : $key_lignes_exists[52];
                         $nb = $this->lignes_caisse[$key_modif]->getQuantite() + 1;
                         $this->setLigneQte($key_modif, $nb);
                    }
                    return true;
               }
               return false;
          }
          return false;
     }

     public function keyLignesExistByRef($ref) {
          // Renvoi un tableau pointant la position des élements ayant la même référence
          $return_keys = array();
          foreach ($this->lignes_caisse as $key => $ligne) {
               if ($ligne->reference == $ref) {
                    $return_keys[$ligne->id_etat_ligne_commande] = $key; // id_etat_ligne_commande
               }
          }
          return $return_keys;
     }

     public function delLigneByRef($del_key) {

          if (key_exists($del_key, $this->lignes_caisse)) {
               unset($this->lignes_caisse[$del_key]);
               $this->setTotaux();
               return true;
          }
          return false;
     }

     public function setStatutLigneByKey($key_ligne_to_change, $id_etat_ligne_commande) {
          if ($key_ligne_to_change != NULL && $id_etat_ligne_commande != NULL) {
               $this->lignes_caisse[$key_ligne_to_change]->id_etat_ligne_commande = $id_etat_ligne_commande;
          }
     }


//    S E T T E R S :: SETTERS
//////////////////////////////////

     public function setTVA() {
          $this->total_tva = 0;

          if ($this->exoneration_tva == 0) {
               $this->setTVA10();
               $this->setTVA20();
               $this->total_tva = $this->total_tva10 + $this->total_tva20;
          }
     }

     public function setTVA10() {
          $this->total_tva10 = 0; // On réinitialise le total de la TVA à 10	
          if (!empty($this->lignes_caisse)) { // S 'il y a des lignes
               foreach ($this->lignes_caisse as $ligne) {
				   
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 10) {
                         $this->total_tva10 += $ligne->getMontantUnitaireHT()*$ligne->getQuantite() * (0.1);
                    }
               }
               $this->total_tva10 = round($this->total_tva10, 2);
          }
     }

     public function setTVA20() {
          $this->total_tva20 = 0; // On réinitialise le total de la TVA à 20	
          if (!empty($this->lignes_caisse)) { // S'il y a des lignes
               foreach ($this->lignes_caisse as $ligne) {
                    $article = articleTable::getArticleByRef($ligne->reference);
                    if ($article->tva == 20)
                         $this->total_tva20 += $ligne->getMontantUnitaireHT()*$ligne->getQuantite() * (0.2);
               }
              $this->total_tva20 = round($this->total_tva20, 2);
          }
     }

     public function setPercentRemiseTTC($remise) {  // Calcul par attribution
	 	if($remise>=0 && $remise<100.01) { // La division par zéro est impossible
			$this->percent_remise = $remise ;
			$this->total_remise_ttc = $this->total_prix_public_ttc * ($remise/100) ;
		}
		//echo $this->total_remise_ttc.'(setPercentRemiseTTC)<br><br>';

	}
	public function setTotalRemiseTTC($remise) { // Calcul par attribution
		if($remise >0 && $this->total_prix_public_ttc>0  && ($remise<$this->total_prix_public_ttc)) {
			// Si la remise et le prix >0 et que la remise n'est pas supérieur au prix
			$this->total_remise_ttc = $remise;
			$this->percent_remise = round($this->total_remise_ttc/$this->total_prix_public_ttc,2)*100;
			//echo $this->total_remise_ttc.'TRTTC(setTotalRemiseTTC)<br><br>';
		}
	}

	public function setTotalRemiseFromLignes() { // Calcul ne se fait que par déduction d'après les lignes
		if (!empty($this->lignes_caisse)) { // S'il y a des lignes
			$total_remise_ttc = $total_remise_ht = 0;
			foreach ($this->lignes_caisse as $ligne) { 

				/*$total_ligne_ttc = $ligne->getQuantite() * $article->prix_public ;
				$ligne_ratio_total = $total_ligne_ttc / $this->total_prix_public_ttc;
				echo $remise_sur_ligne_ttc =  $ligne_ratio_total * $this->total_remise_ttc ;
				$taux_tva = ((100+$article->tva)/100);
				echo $remise_sur_ligne_ht = $remise_sur_ligne_ttc / $taux_tva ;*/
				$remise_sur_ligne_ht = $ligne->getMontantRemiseUnitaireHT()*$ligne->getQuantite();
				$total_remise_ht += round($remise_sur_ligne_ht,2) ;
				
				$article = articleTable::getArticleByRef($ligne->reference);
				$taux_tva = ((100+$article->tva)/100);
				$remise_sur_ligne_ttc = $ligne->getMontantRemiseUnitaireTTC()*$ligne->getQuantite();
				$total_remise_ttc += round($remise_sur_ligne_ttc,2) ;
			}
			$this->total_remise_ht = round($total_remise_ht,2);
			$this->total_remise_ttc = round($total_remise_ttc,2);
			//echo $this->total_remise_ttc.'TRTTC(setTotalRemiseFromLignes)<br><br>';
			//echo $this->total_remise_ht.'TRTTC(setTotalRemiseFromLignes)<br><br>';
		}
	}
	public function setTotalRemiseFromTotal() { // Calcul ne se fait que par déduction d'après la remise globale
		if($this->total_remise_ttc == 0) $this->total_remise_ht = 0;
		// 1. Applique les remises aux lignes proportionnelement
		if (!empty($this->lignes_caisse)) { // S'il y a des lignes
				$total_remise_ht = $total_remise_ttc = 0;
				foreach ($this->lignes_caisse as $ligne) { 
					$article = articleTable::getArticleByRef($ligne->reference);
					$taux_tva = ((100+$article->tva)/100);
	
					// à partir de la proportion de valeur
					$total_ligne_ttc = $ligne->getQuantite() * $ligne->prix_public_ht * $taux_tva;
					$ligne_part_sur_total = $total_ligne_ttc / $this->total_prix_public_ttc;
					
					// On détermine la remise TTC sur la ligne
					$remise_sur_ligne_ttc =  $ligne_part_sur_total * $this->total_remise_ttc ;
					$total_remise_ttc += round($remise_sur_ligne_ttc,2) ;
					
					// On détermine la remise HT sur la ligne
					$remise_sur_ligne_ht = $remise_sur_ligne_ttc / $taux_tva ;
					$total_remise_ht += round($remise_sur_ligne_ht,2) ;
					$remise_unitaire_ht = $remise_sur_ligne_ht/$ligne->getQuantite();
					//echo "<br><br>la ref est ".$ligne->reference;
					//echo "<br>la remise HT sur ligne est ".$remise_sur_ligne_ht;
					//echo "<br>la quantite est ".$ligne->getQuantite();
					//echo "<br>la remise unitaire HT est ".$remise_unitaire_ht;

					//Mise à jour du total unitaire ht et du total remise unitaire ht de la ligne
					$ligne->setMontantRemiseUnitaireHT($remise_unitaire_ht);
	
				}
				$this->total_remise_ht = round($total_remise_ht,2);
				$this->total_remise_ttc = round($total_remise_ttc,2);
				//echo $this->total_remise_ttc.'TRTTC(setTotalRemiseFromTotal)<br><br>';
				//echo $this->total_remise_ht.'TRHT(setTotalRemiseFromTotal)<br><br>';
		}
	// 2. Déduit setTotalRemiseHT()
	}

     public function setLignesCaisseFoire() {
          // Cas d'une caisse déjà en bdd ou des lignes sont déjà existantes // on remplace le contenu des lignes de caisse en cours par celles existantes
          $lignes_caisse_bdd = ligneCaisseFoireTable::getLignesByIdCaisseFoire($this->getCaisseFoireId());
          //echo '<br><br><pre>';
          //var_dump($lignes_caisse_bdd);
          //echo '<br><br></pre>';
          if ($lignes_caisse_bdd == TRUE) {
               return $this->lignes_caisse = $lignes_caisse_bdd;
          }
          $this->setTotaux();
     }
	 
	 public function setLigneCaisseFoireByKey($key,$ligne) {
		$this->lignes_caisse[$key] = $ligne;
	 }


//    S A V E   :: SAVE
//////////////////////////////////
     public function save($update = FALSE) {
          global $pdo;

          if ($update == TRUE) {
               if (caisseTable::getCaisseFoireById($this->id_caisse_foire) == TRUE) { // Si la caisse existe déjà en base de données	
                    $sql = "UPDATE caisse SET ";
                    $set = array();
					
					$this->data = array(
						"total_prix_public_ht" => $this->total_prix_public_ht,
						"total_prix_public_ttc" => $this->total_prix_public_ttc,
						"total_remise_ht" => $this->total_remise_ht,
						"total_remise_ttc" => $this->total_remise_ttc,
						"total_caisse_ht" => $this->total_caisse_ht,
						"total_caisse_ttc" => $this->total_caisse_ttc,
						"info_remise" => $this->info_remise,
						"total_tva" => $this->total_tva,
						"total_tva10" => $this->total_tva10,
						"total_tva20" => $this->total_tva20,
						"payee" => $this->payee,
						"id_statut_caisse" => $this->id_statut_caisse,
					);

                    foreach ($this->data as $att => $value) {
                         $strip_value = $this->stripslashes_array($value);
                         if ($att != 'id' && $att != 'id_caisse_foire' && $att != 'lignes_caisse' && isset($value)) {
                              $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                         }
                    }

                    $sql .= implode(",", $set);
                    $sql .= " WHERE id_caisse_foire =" . $this->id;

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
			$this->data = array(
				"id_client" => $this->id_client,
				"id_vendeur" => $this->id_vendeur,
                "total_prix_public_ht" => $this->total_prix_public_ht,
                "total_prix_public_ttc" => $this->total_prix_public_ttc,
				"total_remise_ht" => $this->total_remise_ht,
                "total_remise_ttc" => $this->total_remise_ttc,
				"total_caisse_ht" => $this->total_caisse_ht,
                "total_caisse_ttc" => $this->total_caisse_ttc,
				"info_remise" => $this->info_remise,
                "total_tva" => $this->total_tva,
				"total_tva10" => $this->total_tva10,
				"total_tva20" => $this->total_tva20,
				"id_statut_caisse" => $this->id_statut_caisse,
				"payee" => $this->payee);

               $sql = " INSERT INTO caisse ";
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
               // Récupérer l'i
               //On réccupère son id d'enregistrement (Last insert id)
               $query = "SELECT id_caisse_foire FROM caisse_foire_foire ORDER BY id_caisse_foire DESC LIMIT 0 , 1";
               try {
                    $stmt = $pdo->query($query);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
               } catch (PDOException $e) {
                    echo "Erreur de select : " . $e;
               }

               if ($res === false)
                    return false;

               $last_id = $res[0]["id_caisse_foire"];
               $this->id_caisse_foire = $last_id;
               $this->id = intval($last_id);

               foreach ($this->lignes_caisse as $ligne) {
                    $values = array(
						"id_caisse_foire" => $last_id,
						"reference" => $ligne->reference,
						"prix_public_ht" => $ligne->getPrixPublicHTFromArticle() ,
						"montant_unitaire_ht" => $ligne->getMontantUnitaireHT() ,
						"montant_remise_unitaire_ht" => $ligne->getMontantRemiseUnitaireHT(),
						"quantite" => $ligne->getQuantite(),
						"id_etat_ligne_commande" => $ligne->id_etat_ligne_commande);
                    $current_ligne = new LigneCaisseFoire($values, true);
                    $current_ligne->save();
               }
          }

          return ($last_id == false) ? NULL : $last_id;
     }
	 
	 
//    G E T T E R S   :: GETTERS
//////////////////////////////////


     public function getCaisseFoireId() {
          return $this->id;
     }
	 public function getIdCaisseFoire() {
          return $this->id_caisse_foire;
     }

     public function getLignesCaisseFoire() {
          $this->setLignesCaisseFoire();
          return $this->lignes_caisse;
     }
	
	  
	 public function getLigneCaisseFoireByKey($key) {
		return $this->lignes_caisse[$key];
	 }

     public function getNbItems() {
          $this->setCaisseFoireQuantite();
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

	public function getTotalPaiements($paiements) {
		// Si les paiements existe
		$total_paiement = 0;
		if($paiements != FALSE) {			
			foreach ($paiements as $paiement) 
			   $total_paiement += $paiement->montant_paiement;
		}
		return $total_paiement;
	}

     public function getTotalRemiseHT() {
          return $this->total_remise_ht;
     }

     public function getTotalRemiseTTC() {
          return $this->total_remise_ttc;
     }

     public function getTotalPrixPublicHT() {
          return $this->total_prix_public_ht;
     }

     public function getTotalPrixPublicTTC() {
          return $this->total_prix_public_ttc;
     }

     public function getTotalCaisseFoireHT() {
          return $this->total_caisse_ht;
     }

     public function getTotalCaisseFoireTTC() {
          return $this->total_caisse_ttc;
     }

     public function getPercentRemise() {
          return $this->percent_remise;
     }

     public function getTotalTVA() {
          return $this->total_tva;
     }
	  public function getTotalTVA10() {
          return $this->total_tva10;
     }
	 public function getTotalTVA20() {
          return $this->total_tva20;
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