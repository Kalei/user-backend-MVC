<?php

/**
 * Description of Feuille
 *
 * @author Rovelli
 */
class FeuilleTechnique extends Technique {
	// La classe feuille est un dérivé de la classe matériel

    public function __construct($row, $lft, $rght, $new = false) {
        parent::__construct($row, $new); 	//J'hérite du constructeur dela classe primitive
        $this->lft = $lft;
        $this->rght = $rght;
    }

    public function display() {
        echo'<li><a href="?action=rediger&id_technique=' . $this->id_technique . '"/>' . $this->nom_court_technique . '</a> => lft: ' . $this->lft . ' rght: ' . $this->rght . '</li>';
    }

}
