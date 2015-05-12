<?php

/**
 * Description of Feuille
 *
 * @author Rovelli
 */
class FeuilleMateriel extends Materiel {

// La classe feuille est un dérivé de la classe matériel

    private $filtred_produits = array();

    public function __construct($row, $new = false) {
        parent::__construct($row, $new);  //J'hérite du constructeur dela classe primitive
    }
/*
    public function getProduitsList() {
        return produitTable::getProduitsByIdMateriel($this->id_materiel);
    }
   
    public function setFiltredList($filtre = array(), $tri = null) {
        echo 'Feuille: ' . $this->nom_materiel . ' ';
        try {
            $tmp_produits = produitTable::getProduitsFiltred($this->id_materiel, $filtre, $tri);
        } catch (Exception $e) {
            echo 'erreur';
        }
        echo count($tmp_produits);
        echo '<br/>';

        if ($tmp_produits != false) {
            $this->filtred_produits = $tmp_produits;
            
           $obj_values = array('url_tri' => $tri,
              'url_marque' => $filtre['url_marque'],
              'url_technique' => $filtre['url_technique'],
              'url_materiel' => $this->url_materiel,
              'id_produit1' => ($this->filtred_produits[0]) ? $this->filtred_produits[0]->id_produit : 0,
              'id_produit2' => ($this->filtred_produits[1]) ? $this->filtred_produits[1]->id_produit : 0,
              'id_produit3' => ($this->filtred_produits[2]) ? $this->filtred_produits[2]->id_produit : 0,
              'id_produit4' => ($this->filtred_produits[3]) ? $this->filtred_produits[3]->id_produit : 0,
              'id_produit5' => ($this->filtred_produits[4]) ? $this->filtred_produits[4]->id_produit : 0);

              $tmp_precalcul = new Precalcul($obj_values, TRUE);
              $tmp_precalcul->save();

            return true;
        } else {
            return false;
        }
    }

    public function getFiltredProduits() {
        return $this->filtred_produits;
    }

    public function getTechniqueList($technique_list = array()) {
        global $pdo;

        foreach ($this->filtred_produits as $value) {
            $article_list = $value->getAssociatedArticles();
            foreach ($article_list as $article) {
                $sql = "SELECT * FROM techniques_2012 WHERE id_technique = " . $article->id_technique1;
                $stmt = $pdo->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($res) > 0) {
                    $technique = $res[0];
                    $technique_list['principale'][$technique['url_technique']] = $technique['nom_court_technique'];
                }

                $sql = "SELECT * FROM techniques_2012 WHERE id_technique = " . $article->id_technique2;
                $stmt = $pdo->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($res) > 0) {
                    $technique = $res[0];
                    $technique_list['principale'][$technique['url_technique']] = $technique['nom_court_technique'];
                }

                $sql = "SELECT * FROM techniques_2012 WHERE id_technique = " . $article->id_technique3;
                $stmt = $pdo->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($res) > 0) {
                    $technique = $res[0];
                    $technique_list['variante'][$technique['url_technique']] = $technique['nom_court_technique'];
                }
            }
        }

        return $technique_list;
    }

    public function getMarqueList() {
        $technique_list = array();
        foreach ($this->filtred_produits as $value) {

            $technique_list[$value->url_marque] = $value->libelle_marque;
        }

        return $technique_list;
    }

    public function display() {
        $produit_list = $this->getProduitsList();
        if (count($produit_list) > 0) {
            echo '<li>' . $this->nom_materiel . ' => lft: ' . $this->lft . ' rght: ' . $this->rght;
            echo '<ul>';
            foreach ($produit_list as $produit) {
                echo "<li>id_produit =" . $produit->id_produit . " | nom = " . $produit->nom . " | prix = " . $produit->prix_produit . "</li>";
            }
            echo '</ul>';
        }
        echo '</li>';
    }

    public function displayWithFiltredProduits() {
        $produit_list = $this->filtred_produits;
        if (count($produit_list) > 0) {
            echo '<li>' . $this->nom_materiel;
            echo '<ul>';
            foreach ($produit_list as $produit) {
                echo "<li>id_produit =" . $produit->id_produit . " | nom = " . $produit->nom . " | prix = " . $produit->prix_produit . "</li>";
            }
            echo '</ul>';
        }
        echo '</li>';
    }
*/
}