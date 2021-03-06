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
	
	public function setQuantite($quantite) {
          $this->quantite = $quantite;
     }

     public function getQuantite() {
          return $this->quantite;
     }
	 
	 public function getFacturability() {
		switch ($this->id_etat_ligne_commande) {
			case(0) : // En attente de traitemennt
				return FALSE ;
				break;
			case(11) : //Prêt et étiqueté
				return FALSE ;
				break;
			case(12) : //Prêt en stock
				return FALSE ;
				break;
			case(21) : //A commander chez le fournisseur
				return FALSE ;
				break;
			case(22) : //Attente Livraison fournisseur moins 7j	
				return FALSE ;
				break;
			case(23) : //Attente Livraison fournisseur plus 7j		
				return FALSE ;
				break;
			case(24) : //Rupture provisoire, attente action client	
				return FALSE ;
				break;
			case(25) : //Rupture définitive, attente action client
				return FALSE ;
				break;
			case(26) : //Attente Livraison fournisseur illimitée
				return FALSE ;
				break;
			case(27) : //Vérifier stock magasin
				return FALSE ;
				break;
			case(31) : //Ligne échangée
				return FALSE ;
				break;
			case(41) : //Annulée	
				return FALSE ;
				break;
			case(51) : //Expédiée
				return TRUE ;
				break;
			case(52) : //Retiré en magasin
				return TRUE ;
				break;
			default : return FALSE ;
	
		}
	}

}
?>