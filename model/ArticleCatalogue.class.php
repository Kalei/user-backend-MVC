<?php

class ArticleCatalogue {

    private $data;
    private $id;

    public function __construct($row, $new = false) {
        ini_set('memory_limit', '512M');
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
