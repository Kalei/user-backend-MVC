<?php

/**
 * Description of Noeud
 * La classe noeud est un dérivé de la classe materiel
 *
 * @author Rovelli
 */
class NoeudCategorie extends Materiel {

    protected $categories = array();
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
                $this->children[$child_key] = new NoeudCategorie($child_value); // Peut-on parler ici de récursivité ?
            }
            // Si l'enfant est une feuille
            else {
                $this->children[$child_key] = new FeuilleCategorie($child_value);
            } // Alors la droite = gauche +1
        }
    }

    public function getNbChild() { // Détermine le nombre d'enfant
        $nb_child = 0;

        //echo '<hr/><h4>' . $this->nom_materiel . '</h4> ';
        foreach ($this->children as $child) {
            ++$nb_child; // par chaque enfant je compte au moins 1
            if (get_class($child) == "Noeud")
                $nb_child += $child->getNbChild(); // Si en plus c'est c'est un noeud on compte le nombre d'enfant de ce noeud		
        }
        return $nb_child;
    }

    public function setCategories($categories = array(), $save = false) {
        ob_implicit_flush(TRUE);
        $tmp_categories = array();
        $i = 0;


        //$exist_categories = categorieTable::getCategorieByIdMateriel($this->id_materiel);
        //var_dump($exist_categories);
        // Parcours des enfants
        foreach ($this->children as $child) {
            // echo $child->nom_materiel.'<br />'; flush();  ob_flush();
            // récursivité
            if (get_class($child) == "NoeudCategorie") {
                $verif = $child->setCategories($categories, $save); // Le parcours de l'enfant soit dans un noeud soit dans une feuille
                // Servira à remplir $this->categories via un tableau temporaires grâce aux tableaux tableau de catégories des enfants.
            } else {
                $verif = $child->setCategories($categories);
            }
            if (!is_array($child->getCategories())) {
                echo '<p style="color="red";">"Not found in categories table: ' . $child->nom_materiel . '"</p>';
                flush();
                ob_flush();
            } else {
                $tmp_categories = array_merge($tmp_categories, $child->getCategories());
            }
        }

        echo 'Nous sommes dans le materiel ' . $this->nom_materiel . '<pre>';
        //var_dump($this->categories);
        // Chaque noeud Categorie materiel contient un tableau (listing) de categories (objet Categories) 
        //=> voir ligne 11 protected $categories = array();
        $this->categories = $this->setCategoriesProduits($tmp_categories, $save);
        echo '<hr/>';
        flush();
        ob_flush();

        return true;
    }

    public function setCategoriesProduits($tmp_categories, $save = false) {
        $tmp_couples = array();
        $to_merge_categories = array();

        foreach ($tmp_categories as $tmp_categorie) {
            $actual_couple = array('url_tri' => $tmp_categorie->url_tri, 'id_technique' => $tmp_categorie->id_technique, 'id_marque' => $tmp_categorie->id_marque);
            /* echo 'Couple actuel : <br/><pre>';
              var_dump($actual_couple);
              echo '</pre>'; */

            if (in_array($actual_couple, $tmp_couples) == FALSE) {
                $tmp_couples[] = $actual_couple;

                //echo 'Je suis un nouveau couple<br/>';
                //On initialise les tableaux de produit au cas ou un est vide
                for ($i = 1; $i <= 5; $i++) {
                    ${'tmp_produit' . $i . '_list'} = array();
                }

                foreach ($tmp_categories as $key_bis => $tmp_categorie_bis) {
                    if (array('url_tri' => $tmp_categorie_bis->url_tri, 'id_technique' => $tmp_categorie_bis->id_technique, 'id_marque' => $tmp_categorie_bis->id_marque) == $actual_couple) {
                        //Permet d'avoir l'ensemble des produits correspondants à un couple.
                        for ($i = 1; $i <= 5; $i++) {
                            if ($tmp_categorie_bis->{'id_produit' . $i} != 0 && !in_array($tmp_categorie_bis->{'id_produit' . $i}, ${'tmp_produit' . $i . '_list'})) {
                                ${'tmp_produit' . $i . '_list'}[] = $tmp_categorie_bis->{'id_produit' . $i};
                            }
                        }
                    }
                }

                $tmp_produit_list = array();
                for ($i = 1; $i <= 5; $i++) {
                    for ($j = 0; $j < count(${'tmp_produit' . $i . '_list'}); $j++) {
                        (${'tmp_produit' . $i . '_list'}[$j] != 0) ? $tmp_produit_list[] = ${'tmp_produit' . $i . '_list'}[$j] : '';
                    }
                }

                $tmp_produit_list = array_values(array_unique($tmp_produit_list));


                if ($tmp_produit_list[0] != 0 && count($tmp_produit_list) > 0) {
                    $new_categorie = new Categorie(array(
                        'id_materiel' => $this->id_materiel,
                        'id_marque' => $actual_couple['id_marque'],
                        'url_tri' => $actual_couple['url_tri'],
                        'id_technique' => $actual_couple['id_technique'],
                        'id_produit1' => (!empty($tmp_produit_list[0])) ? $tmp_produit_list[0] : '0',
                        'id_produit2' => (!empty($tmp_produit_list[1])) ? $tmp_produit_list[1] : '0',
                        'id_produit3' => (!empty($tmp_produit_list[2])) ? $tmp_produit_list[2] : '0',
                        'id_produit4' => (!empty($tmp_produit_list[3])) ? $tmp_produit_list[3] : '0',
                        'id_produit5' => (!empty($tmp_produit_list[4])) ? $tmp_produit_list[4] : '0'
                            ), true);

                    $to_merge_categories[] = $new_categorie;

                    //echo 'Je suis donc une toute nouvelle categorie<br/>';
                    //var_dump($new_categorie);
                    if ($save == true) {
                        $new_categorie->save();

                        //echo 'Et normalement je devrait me save<hr>';
                        flush();
                        ob_flush();
                    }
                }
            }
        }
        return $to_merge_categories;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function display() {
        echo'<li>Nombre de catégories' . count($this->categories) . '</li><ul>';
        foreach ($this->children as $child) {
            $child->display();
        }
        echo'</ul>';
        flush();
    }

}
