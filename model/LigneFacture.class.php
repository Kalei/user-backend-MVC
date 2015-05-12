<?php

/**
 * Description of LigneFacture
 *
 * @author Jean-Loup Couegnas
 */
class LigneFacture {

    private $data;
    private $id;
	// On stocke la valeur du prix public pour le cas où le prix de la référence change dans la table Article

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
            $sql = " UPDATE lignefacture SET ";
            $set = array();

            foreach ($this->data as $att => $value) {
                $strip_value = $this->stripslashes_array($value);
                if ($att != 'id' && $att != 'id_lignefacture' && $att != 'id_facture' && isset($value)) {
                    $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                }
            }

            $sql .= implode(",", $set);
            $sql .= " WHERE id_lignefacture =" . $this->id;

            $last_id = $this->id;
            try {
                echo $sql;
                $pdo->exec($sql);
            } catch (PDOException $e) {
                echo $e;
            }
        } else {

            //Insertion d'un nouvel élément
            $sql = " INSERT INTO lignefacture ";
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
            $query = "SELECT id_lignefacture FROM lignefacture ORDER BY id_lignefacture DESC LIMIT 0 , 1";
            try {
                $stmt = $pdo->query($query);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Erreur de select : " . $e;
            }

            if ($res === false)
                return false;

            //var_dump($res);

            $last_id = $res[0]["id_lignefacture"];
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

	public function	getPrixPublicHT(){
		return $this->prix_public_ht;
	}
	public function	getRemiseHT(){
		return $this->remise_ht;
	}
	public function	getRemisePercent(){
		$percent = round((($this->montant_unitaire_ht/$this->prix_public_ht)-1)*100);
		return $percent;
	}
	public function getMontantLigneHT(){
		//if(empty($this->montant_unitaire_ht)) $this->montant_unitaire_ht = ($this->prix_public_ht - $this->remise) * $this->quantite ;
		return $this->montant_unitaire_ht ;
	}
}
?>