<?php

/**
 * Description of Feuille
 *
 * @author Rovelli
 */
class FeuilleCategorie extends FeuilleMateriel {

    protected $categories = array();

    // La classe feuille est un dérivé de la classe matériel
    public function __construct($row, $new = false) {
        parent::__construct($row, $new);  //J'hérite du constructeur dela classe primitive
    }

    public function setCategories($categories = array()) {
        if (!empty($categories)) {
            foreach ($categories as $key => $categorie) {
                if ($categorie->id_materiel == $this->id_materiel) {
                    $this->categories[] = $categorie;
                }
            }
        } else {
            $this->categories = categorieTable::getCategorieByIdMateriel($this->id_materiel);
        }
        return true;
    }

    public function getCategories() {
        return $this->categories;
    }

}
