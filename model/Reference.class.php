<?php

/**
 * Description of Reference
 *
 * @author Jean-Loup Couegnas
 */
class Reference {

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
            $sql = " UPDATE article SET ";
            $set = array();

            foreach ($this->data as $att => $value) {
                $strip_value = $this->stripslashes_array($value);
                if ($att != 'id' && $att != 'reference' && isset($value)) {
                    $set[] = " $att = " . $pdo->quote($strip_value) . " ";
                }
            }

            $sql .= implode(",", $set);
            $sql .= " WHERE reference =" . $this->id;

            $last_id = $this->id;
            try {
                //echo $sql;
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

}
?>