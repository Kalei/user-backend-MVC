<?php

/**
 * Classe permettant d'organiser les données materiels
 *
 * @author Rovelli
 */
class ArbreTechnique {

    protected $racine;

    public function __construct() {
        $this->racine = $this->getRacineFromTable();
    }

	private function getRacineFromTable() {
		global $pdo;
		global $table;
		
		$sql = "SELECT * FROM techniques WHERE id_technique=1";
		
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if ($res === false) return false;
		
		return new NoeudTechnique($res[0], 1);
	}

    public function display() {
        echo '<ul>';
        $this->racine->display();
        echo '</ul>';
    }

    public function save() {
        $this->racine->save();
        $this->racine->saveChild();
    }

}
