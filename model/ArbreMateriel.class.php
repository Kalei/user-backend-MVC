<?php

/**
 * Classe permettant d'organiser les données materiels
 *
 * @author Rovelli
 */
class ArbreMateriel {
	/*
	
	Construire un arbre materiel pour les Categories
	
	*/
	
    protected $racine;

    public function __construct($selected = null) {
        if ($selected != null) {
            $this->racine = materielsTable::getFeuilleOrNoeudByUrl($selected);
        } else {
            $this->racine = $this->getRacineFromTable();
        }
    }

    private function getRacineFromTable() {
        global $pdo;
        $sql = "SELECT * FROM materiels WHERE id_materiel=159";

        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0) {
            return false;
        }

        return new NoeudMateriel($res[0]);
    }

    public function getRacine() {
        return $this->racine;
    }

    public function display() {
        echo '<ul>';
        $this->racine->display();
        echo '</ul>';
    }

    public function getTechniqueList() {
        $technique_list = $this->racine->getTechniqueList();
        return $technique_list;
    }

    public function getMarqueList() {
        $marque_list = $this->racine->getMarqueList();
        return $marque_list;
    }

}
