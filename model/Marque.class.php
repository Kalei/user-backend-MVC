<?php

/**
 * Description of Marque
 *
 * @author Rovelli
 */
class Marque {

    private $data;
    private $id;

    public function __construct($row, $new = false) {
        $i = 0;
        foreach ($row as $key => $value) {
            //On précise lors de la création d'un objet s'il sagit d'une nouvelle entrée
            if ($i == 0 && $value != null && $new == false) {
                $this->id = $value;
            }
            $this->$key = addslashes($value);
            $i++;
        }
    }

    public function __get($prop) {
        return htmlspecialchars($this->data[$prop]);
    }

    public function __set($prop, $value) {
        $this->data[$prop] = $value;
    }

}
