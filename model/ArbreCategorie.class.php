<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('memory_limit', '750M');

class ArbreCategorie extends ArbreMateriel {

    public function __construct($categories = array(), $save = false, $selected = null) {
	    // Contruit l'arbre à partir d'une racine définie (par exemple 'moulinets')
        if ($selected != null) {
            $this->racine = materielsTable::getFeuilleOrNoeudCategorieByUrl($selected);
        }
	    // Contruit l'arbre à partir de la racine matériel
	   else {
            $this->racine = $this->getRacineFromTable();
        }
        $this->setCategories($categories, $save);
    }

    private function getRacineFromTable() {
        global $pdo;
        $sql = "SELECT * FROM materiels WHERE id_materiel=159";

        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0) {
            return false;
        }

        return new NoeudCategorie($res[0]);
    }

    /**
     * 
     * @param array $filtre -> Tableau filtre marque, technique1, technique2, specialite
     * @param String $global_filtre -> 'pas-cher', 'meilleures-ventes', 'nouveautes', 'top' ou default ( = null)
     */
    public function setCategories($categories = array(), $save = false) {
        return $this->racine->setCategories($categories, $save); // Ce setCategories est associé à l'objet NoeudCategorie
    }

}
