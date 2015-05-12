<?php

/**
 * Description of Noeud
 * La classe noeud est un dérivé de la classe materiel
 *
 * @author Rovelli
 */
class NoeudMateriel extends Materiel {

    protected $children;

    public function __construct($row, $new = false) {

        parent::__construct($row, $new); //J'hérite du constructeur dela classe primitive
        $this->setChildandRght();
    }

    private function setChildandRght() {
        global $pdo;

        $sql = "SELECT * FROM materiels WHERE id_parent= " . $this->id_materiel . " ORDER BY ordre ASC";
        $stmt = $pdo->query($sql);
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // cas n° 1 : il n'y pas d'enfant
        if ($children === false)
            return false;

        // cas n°2 : il y a des enfants, 
        // on fait un parcours pour déterminer la position des enfants et en déduire la position de rght
        foreach ($children as $child_key => $child_value) {

            // On compte le nombre d'enfant pour ce materiel
            $nb_child_value = materielsTable::getChildByIdNoeud($child_value['id_materiel']);

            // Si l'enfant est lui même un noeud
            if ($nb_child_value > 0) {
                $this->children[$child_key] = new NoeudMateriel($child_value); // Peut-on parler ici de récursivité ?
            }
            // Si l'enfant est une feuille
            else
                $this->children[$child_key] = new FeuilleMateriel($child_value); // Alors la droite = gauche +1
        }
    }

    public function getNbChild() { // Détermine le nombre d'enfant
        $nb_child = 0;

        //echo '<hr/><h4>' . $this->nom_materiel . '</h4> ';
        foreach ($this->children as $child) {
            ++$nb_child; // par chaque enfant je compte au moins 1
            if (get_class($child) == "NoeudMateriel")
                $nb_child += $child->getNbChild(); // Si en plus c'est c'est un noeud on compte le nombre d'enfant de ce noeud		
        }
        return $nb_child;
    }

    public function display() {
        echo'<li>' . $this->nom_materiel . ' => lft: ' . $this->lft . ' rght: ' . $this->rght . '</li><ul>';
        foreach ($this->children as $child) {
            $child->display();
        }
        echo'</ul>';
    }

    public function saveChild() {
        foreach ($this->children as $child) {
            if (get_class($child) == "NoeudMateriel") {
                $child->save();
                $child->saveChild();
            } else {
                $child->save();
            }
        }
    }
}