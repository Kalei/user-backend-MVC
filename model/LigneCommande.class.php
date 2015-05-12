<?php

/**
 * Description of LigneCommande
 *
 * @author Rovelli
 */
class LigneCommande {

    private $data;
    private $id;

    public function __construct($row, $new = false) {
        global $pdo;
        $i = 0;
        foreach ($row as $key => $value) {
            if ($i == 0 && $value != null && $new == false) {
                $this->id = $value;
            }
            $this->$key = addslashes($value);
            $i++;
        }
    }

    public function save() {
        global $pdo;

        $class = strtolower(get_class($this));

        if (!empty($this->id)) {
            $sql = " UPDATE lignecommande SET ";
            $set = array();

            foreach ($this->data as $att => $value)
                if ($att != 'id' && $att != 'idlignecommande' && $value)
                    $set[] = " $att = " . $pdo->quote($value) . " ";

            $sql .= implode(",", $set);
            $sql .= " WHERE idlignecommande =" . $this->idlignecommande;

            $last_id = $this->id;
            try {
                $pdo->exec($sql);
            } catch (PDOException $e) {
                echo $e;
            }
        } else {

            $sql = " INSERT INTO lignecommande ";
            $sql .= "(" . implode(",", array_keys($this->data)) . ") ";
            foreach (array_values($this->data) as $value)
                $values[] = $pdo->quote($value);
            $sql .= "values (" . implode(",", array_values($values)) . ")";

            try {
                $pdo->exec($sql);
            } catch (PDOException $e) {
                echo "Erreur d'insert : " . $e;
            }

            $query = "SELECT idlignecommande FROM lignecommande ORDER BY idlignecommande DESC LIMIT 0 , 1";
            try {
                $stmt = $pdo->query($query);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Erreur de select : " . $e;
            }

            if ($res === false)
                return false;

            $last_id = $res[0]["idlignecommande"];
        }

        return $last_id == false ? NULL : $last_id;
    }

    public function __get($prop) {
        if ($prop == 'id') {
            return $this->data['idlignecommande'];
        } else {
            return htmlspecialchars($this->data[$prop]);
        }
    }

    public function __set($prop, $value) {
        $this->data[$prop] = $value;
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
