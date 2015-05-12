<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of articleCatalogueTable
 *
 * @author Rovelli
 */
class articleCatalogueTable {

    public static function getArticles() {
        global $pdo;
        
        $sql = "SELECT p.idmarque, a.id_technique1, a.id_technique2, a.id_technique3, a.id_materiel1 "
                . "FROM article a, produit p "
                . "WHERE p.id_produit = a.id_produit AND a.actif=1 AND (a.statut=11 OR a.statut=12 OR a.statut=13) AND a.id_materiel1>0 ";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //echo count($res);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new ArticleCatalogue($value);
        }

        return $utable;
    }

}
