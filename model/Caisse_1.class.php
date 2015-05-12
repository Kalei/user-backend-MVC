<?php

/**
 * Objet panier.
 * 
 * V1 @author Hugo Rovelli  => Décembre 2014
  v1.2 @author Jean-Loup Couegnas => Janvier 2015
  v1.3 @author Hugo Rovelli => Février 2015 (en dév).
  
  	• évolutions sur lignes:
  	- dans les lignes de caisses : Sauvegarde de la remise/ligne et du taux de TVA/ligne
  	- Statut des lignes : retirée, à commander, en cours de reception, annulée
	• éditer une caisse ouverte
	• sur le l'historique afficher le détail d'une caisse dans une pop up fancy box ?
	• gestion des paiements table paiement etc ...
	• si caisse empty, listing des 5 derniers caisses (permet de vérifier que tout va bien)
	• plusieurs caisses en parallèles
	• impression par jour des lignes de caisses et taux tva -> sauvegarde papier (30 feuilles / mois + 1 feuille de récap. mensuel)
 */
class Caisse {

// private implique que je ne peux acceder à ses variables qu'à travers l'une des fonctions de l'objet (GETTER et SETTER)
     private $id; // $row['id_caisse'] est forcement une string , $id sera un entier
	 private $lignes_caisse ; 
     private $nb_references;
     private $nb_items;
     private $total_pvc;
     private $total_prix_public;
     private $total_caisse;
     public $id_caisse;
     public $id_client;
     public $id_user;
	 public $CHQ;
	 public $ESP;
	 public $CB;
     //public $id_modepaiement;
     private $percent_remise;
     public $montant_remise;
     private $promos;
     private $data;
     public $montant_tva10;
     public $montant_tva20;
     public $montant_tva;

// __construct () sert à initialiser les valeurs des variables de l'objet
	public function __construct($row = null) {
		// Valeurs par défaut
		$this->id = 0 ;
		$this->id_caisse= 0 ;
		$this->lignes_caisse = array();
		$this->nb_references = 0;
		$this->nb_items = 0;
		$this->total_pvc = 0;
		$this->total_prix_public = 0;
		$this->total_caisse = 0;
		$this->percent_remise = 0;
		$this->montant_remise = 0;
		$this->id_client = 1;
		//$this->id_modepaiement = ''; //ESP, CB, CHQ
		$this->id_user = 1;
		$this->data = array();
		$this->montant_tva10 = 0;
		$this->montant_tva20 = 0;
		$this->montant_tva = 0;
		$this->ESP = 0;
		$this->CHQ = 0;
		$this->CB = 0;
		
		// Valeurs obtenus par $row
		if ($row != null) {
		   $i = 0;
		   foreach ($row as $key => $value) {
		//On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
				if ($i == 0 && $value != null ) { //&& $new == false
					 $this->id_caisse = $value;
				}
				$this->$key = $value;
				$i++;
		   }
		}
		$this->id = intval($this->id_caisse);
		if( $this->id == 0) {
			$last_id_caisse = caisseTable::getLastInsertId();
			$this->id = ++$last_id_caisse ;
		}
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
			$this->total_pvc += $article->getPvc()*$ligne->quantite;
			$this->total_prix_public += $article->getPrixPublic()*$ligne->quantite;
          }
          $this->total_caisse = ($this->total_prix_public - $this->getMontantRemise()) - ($this->total_prix_public  * $this->percent_remise);
     }

     public function setTotalCaisse() {
		 $this->setTotalPrixPublic();
         $this->total_caisse = ($this->total_prix_public ) - ($this->total_prix_public  * $this->percent_remise) - $this->getMontantRemise();
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

     public function addLigne($refAdd, $nb = 1, $reset_qte = FALSE) {
		if (!empty($refAdd)) {
			$exist = false;
			
			// Test la référence existe déjà dans le panier
			if($this->getLignesCaisse()!= FALSE) {
               foreach ($this->getLignesCaisse() as $refExist) {
				  // echo $refExist->reference. ' # '.$refExist->quantite.'<br>';
                    if ($refAdd == $refExist->reference) {
						 if($reset_qte == TRUE) $refExist->setQuantite($nb); // J'ajoute la quantité à la quantité initiale
						 else $refExist->setQuantite($refExist->getQuantite()+$nb); // J'ajoute la quantité à la quantité initiale
                         $exist = true;
                    }
 				}
			}
			// C'est une référence qui n'est pas encore dans le panier
            if ($exist == false) {
				$ligne = array( "id_caisse" => $this->getCaisseId(), "reference" =>$refAdd, "quantite" => $nb);
				$ligneAdd = new LigneCaisse($ligne);
				array_push($this->lignes_caisse, $ligneAdd);
			    //$refAdd->setQuantite($nb);
			}
			$this->setCaisseQuantite();
			$this->setTotalPrixPublic();
			$this->setTVA();
		}
		return false;
	}

	public function delLigneByRef($del_ligne) {
	  if ($del_ligne != NULL && $del_ligne != "") {
		   foreach ($this->lignes_caisse as $key => $ligne) {
				if ($del_ligne == $ligne->reference) {
					 unset($this->lignes_caisse[$key]);
				}
		   }
	  }
	  $this->setCaisseQuantite();
	  $this->setTotalPrixPublic();
	  $this->setTVA();
	}

// Fonctions relatives à la TVA
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••	


	
    /* public function setTVA() { // V1
		
		if (isset($this->montant_remise) && $this->total_prix_public > 0)  // S'il y a une remise en €
			$total_percent_remise = ($this->total_prix_public - $this->montant_remise) / $this->total_prix_public;
		elseif (isset($this->percent_remise) && $this->total_prix_public > 0) // S'il y a une remise en %
			$total_percent_remise = $this->percent_remise;
		else  $total_percent_remise = 1;// S'il n'y a pas de remise
		// echo 'pct total remise : ' . $total_percent_remise;
		
		$this->montant_tva = $this->montant_tva20 = $this->montant_tva10 = 0;
		
		// s'il n'y as pas de ligne de Caisse, le setter récuppère les lignes en bdd
		if(empty($this->lignes_caisse)) $this->setLignesCaisse();
		
		if(!empty($this->lignes_caisse)) { // S 'il y a des lignes
			foreach ($this->lignes_caisse as $ligne) {
				$article = articleTable::getArticleByRef($ligne->reference);
				$remise_proportionnelle = $this->montant_remise /  ($article->prix_public * $ligne->getQuantite()) ;
				$prix_vente_article = $article->prix_public - $remise_proportionnelle;

				if ($article->tva == 20) {
					$tva = 1 + $article->tva / 100;
					$this->montant_tva20 += ($prix_vente_article - ($prix_vente_article / $tva)) * $total_percent_remise * $ligne->getQuantite();
				} else {
					$tva = 1 + $article->tva / 100;
					$this->montant_tva10 += ($prix_vente_article - ($prix_vente_article / $tva)) * $total_percent_remise * $ligne->getQuantite();
				}
			}
		}
		
		$this->montant_tva10 = round($this->montant_tva10,2);
		$this->montant_tva20 = round($this->montant_tva20,2);
		$this->montant_tva = $this->montant_tva10 + $this->montant_tva20 ;
	}*/
	
	
	public function setTVA() { 
		$this->setTVA10();
		$this->setTVA20();
		$this->montant_tva =  $this->montant_tva10 + $this->montant_tva20;
	}
	public function setTVA10() {
		$this->montant_tva10 = 0; // On réinitialise le montant de la TVA à 10	
		if(empty($this->lignes_caisse)) $this->setLignesCaisse();
		if(!empty($this->lignes_caisse)) { // S 'il y a des lignes
			foreach ($this->lignes_caisse as $ligne) {
				$article = articleTable::getArticleByRef($ligne->reference);
				// Remise Ligne = Montant Remise * Total Ligne / Total Caisse hors remise
				$remise_ligne = ($this->montant_remise *  ($article->prix_public * $ligne->getQuantite())) /  $this->getTotalPrixPublic() ;
				$prix_vente_ligne = $article->prix_public* $ligne->getQuantite() - $remise_ligne ;
				if ($article->tva == 10) $this->montant_tva10 += ($prix_vente_ligne - ($prix_vente_ligne / 1.1)) ;
			}
			$this->montant_tva10 = round($this->montant_tva10,2);
		}
	}
	public function setTVA20() {
		$this->montant_tva20 = 0;
		if(empty($this->lignes_caisse)) $this->setLignesCaisse();	
		if(!empty($this->lignes_caisse)) { // S'il y a des lignes
			foreach ($this->lignes_caisse as $ligne) {
				$article = articleTable::getArticleByRef($ligne->reference);
				// Remise Ligne = Montant Remise * Total Ligne / Total Caisse hors remise
				$remise_ligne = ($this->montant_remise *  ($article->prix_public * $ligne->getQuantite())) /  $this->getTotalPrixPublic() ;
				$prix_vente_ligne = $article->prix_public* $ligne->getQuantite() - $remise_ligne ;
				if ($article->tva == 20) $this->montant_tva20 += ($prix_vente_ligne - ($prix_vente_ligne / 1.2)) ;
			}
			$this->montant_tva20 = round($this->montant_tva20,2);
		}	
	}

     public function setPercentRemise($remise) {
          $this->percent_remise = $remise / 100;
          $this->setCaisseQuantite();
          $this->setTotalPrixPublic();
          $this->setTVA();
     }

     public function setMontantRemise($remise) {
          $this->montant_remise = $remise;
          $this->setCaisseQuantite();
          $this->setTotalPrixPublic();
          $this->setTVA();
     }
	public function setLignesCaisse() {
		// Cas d'une caisse déjà en bdd ou des lignes sont déjà existantes // on remplace le contenu des lignes de caisse en cours par celles existantes
        $lignes_caisse_bdd = ligneCaisseTable::getLignesByIdCaisse($this->getCaisseId()) ;
		//echo '<br><br><pre>';
		//var_dump($lignes_caisse_bdd);
		//echo '<br><br></pre>';
		if($lignes_caisse_bdd == TRUE) {
			return $this->lignes_caisse = $lignes_caisse_bdd;
		}
		$this->setTotalPrixPublic();
    }

	public function save($update = FALSE) {
		global $pdo;
						
		if($update == TRUE){
			if(caisseTable::getCaisseById($this->getCaisseId()) == TRUE) { // Si la caisse existe déjà en base de données	
				$sql = "UPDATE caisse SET ";
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
			} 
			else echo "Vous essayer de mettre à jour une caisse qui n'est pas (ou plus) repertoriée dans la base de données";

		} 
		else { //Insertion d'un nouvel élément
		  
			$this->data = array("id_client" => $this->id_client, "id_user" => $this->id_user,
			   "montant_remise" => $this->montant_remise + ($this->total_prix_public  * $this->percent_remise), "montant_vente" => $this->getTotalCaisse(),
			   "CHQ" => $this->CHQ, "ESP" => $this->ESP, "CB" => $this->CB);
			
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
			// R2cupérer l'i
			//On réccupère son id d'enregistrement (Last insert id)
			$query = "SELECT id_caisse FROM caisse ORDER BY id_caisse DESC LIMIT 0 , 1";
			try {
				$stmt = $pdo->query($query);
				$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				echo "Erreur de select : " . $e;
			}
			
			if ($res === false) return false;	
		
			$last_id = $res[0]["id_caisse"];
			$this->id_caisse = $last_id;
			$this->id = intval($last_id);
		
		   foreach ($this->lignes_caisse as $ligne) {
				$values = array("id_caisse" => $last_id, "reference" => $ligne->reference, "quantite" => $ligne->getQuantite());
				$current_ligne = new LigneCaisse($values, true);
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
		return $this->lignes_caisse ;
	}
	

     public function getNbItems() {	
	 	$this->setCaisseQuantite(); 
         return $this->nb_items;
     }

     public function getClient() {
          if ($this->id_client > 0) {
               return clientTable::getClientById($this->id_client);
		  }
     }

     public function getNbReferences() {
          return $this->nb_references;
     }

     public function getTotalPrixPublic() {
          return $this->total_prix_public;
     }
	
     public function getTotalCaisse() {
          return $this->total_prix_public  - ($this->total_prix_public  * $this->percent_remise) - $this->montant_remise;
     }

     public function getMontantRemise() {
          return $this->montant_remise;
     }

     public function getPercentRemise() {
          return $this->percent_remise;
     }
	  public function getMontantTVA() {
		  $this->setTVA();
          return $this->montant_tva;
     }
	 public function getMontantTVA10() {
		  $this->setTVA10();
          return $this->montant_tva10;
     }
	 public function getMontantTVA20() {
		  $this->setTVA20();
          return $this->montant_tva20;
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