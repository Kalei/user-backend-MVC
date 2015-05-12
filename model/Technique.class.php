<?php

class Technique {

    private $data;
    private $id;

    public function __construct($row, $new = false) {
        global $pdo;
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
	public function save() {
		global $pdo;
		
		$class = strtolower(get_class($this));
	
		if (!empty($this->id)) {
			$sql = " UPDATE techniques SET ";
			$set = array();
			
			foreach ($this->data as $att => $value)
				if ($att != 'id' && $att != 'id_technique' && $att != 'children' && $value)
					 $set[] = " $att = " . $pdo->quote($value) . " ";

	
			$sql .= implode(",", $set);
			$sql .= " WHERE id_technique =" . $this->id;
	
			$last_id = $this->id;
			try {
				//echo $sql;
				$pdo->exec($sql);
				//echo $this->nom_materiel . "<hr/>";
			} catch (PDOException $e) {
				echo $e;
			}
		}
		else {
	
			//Insertion d'un nouvel élément
			$sql = " INSERT INTO techniques ";
			$sql .= "(" . implode(",", array_keys($this->data)) . ") ";
			foreach(array_values($this->data) as $value) $values[] = $pdo->quote($value);
			$sql .= "values (" . implode(",", array_values($values)) . ")";
			
			//echo $sql;
			
			try {
				$pdo->exec($sql);
			} catch (PDOException $e) {
				echo "Erreur d'insert : " . $e;
			}


			//On réccupère son id d'enregistrement (Last insert id)
			$query = "SELECT id_technique FROM techniques ORDER BY id_technique DESC LIMIT 0 , 1";
			try {
				$stmt = $pdo->query($query);
				$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				echo "Erreur de select : " . $e;
			}
			
			if ($res === false) return false;
			
			//var_dump($res);
			
			$last_id = $res[0]["id_technique"];
		}
			
		return $last_id == false ? NULL : $last_id;
	}

	public function __get($prop) {
		return htmlspecialchars($this->data[$prop]);
	}
	
	public function __set($prop, $value) {
		$this->data[$prop] = $value;
	}
}
?>