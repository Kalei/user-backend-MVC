<?php
/**
 * Description of Noeud
 * La classe noeud est un dérivé de la classe Technique
 *
 * @author Rovelli
 */
class NoeudTechnique extends Technique {

	protected $children;

	public function __construct($row, $lft_parent = 0, $rght_parent = 0, $new = false) {
		parent::__construct($row, $new);	//J'hérite du constructeur dela classe primitive
		$this->lft = $lft_parent;
		$this->rght = $lft_parent;
		$this->setChildandRght();
	}
	
	private function setChildandRght() {
		global $pdo ;

		$sql = "
			SELECT * FROM techniques
			WHERE id_parent= " . $this->id_technique.'
			ORDER BY ordre_technique ASC';
		$stmt = $pdo->query($sql);
		$children = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		// cas n° 1 : il n'y pas d'enfant
		if ($children === false) return false;
		
		// cas n°2 : il y a des enfants, 
		
		$tmp_lft = $this->lft; // point de départ, la position du parent
	
		// on fait un parcours pour déterminer la position des enfants et en déduire la position de rght
		foreach ($children as $child_key => $child_value) {
			++$tmp_lft; // incremente la position lft d'une case pour chaque enfant
			
			// On compte le nombre d'enfant pour ce technique
				$nb_child_value = techniqueTable::getChildByIdNoeud($child_value['id_technique']);
			
			// Si l'enfant est lui même un noeud
			if ($nb_child_value > 0) {
				$this->children[$child_key] = new NoeudTechnique($child_value, $tmp_lft); // Peut-on parler ici de récursivité ?
				$tmp_lft = $tmp_lft + $this->children[$child_key]->getNbChild() * 2 + 1;
			}
			// Si l'enfant est une feuille
			else $this->children[$child_key] = new FeuilleTechnique($child_value, $tmp_lft, ++$tmp_lft); // Alors la droite = gauche +1
		}
		// La valeur de Rght équivaut au dernier enfant +1 
		$this->rght = $this->children[count($this->children) - 1]->rght + 1;
	}


	public function getNbChild() {	// Détermine le nombre d'enfant
		$nb_child = 0;

		//echo '<hr/><h4>' . $this->nom_court_technique . '</h4> ';
		foreach ($this->children as $child) {
			++$nb_child; // par chaque enfant je compte au moins 1
			if (get_class($child) == "NoeudTechnique") $nb_child += $child->getNbChild();	// Si en plus c'est c'est un noeud on compte le nombre d'enfant de ce noeud		
		}
		return $nb_child;
	}

    public function display() {
        echo '<li><a href="?action=rediger&id_technique=' . $this->id_technique . '">' . $this->nom_court_technique . '</a> => lft: ' . $this->lft . ' rght: ' . $this->rght . '</li><ol>';
        foreach ($this->children as $child) {
            if (get_class($child) == "NoeudTechnique") {
                $child->display();
            } else {
                $child->display();
            }
        }
        echo'</ol>';
    }

    public function saveChild() {
        foreach ($this->children as $child) {
            if (get_class($child) == "NoeudTechnique") {
                $child->save();
                $child->saveChild();
            } else {
                $child->save();
            }
        }
    }
}