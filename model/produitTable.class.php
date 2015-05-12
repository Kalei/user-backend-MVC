<?php

class produitTable {

     public static function getProduitById($id) {
          global $pdo;

          $sql = "SELECT * FROM produit WHERE id_produit=$id";
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($res))
               return false;

          return new Produit($res[0]);
     }

     public static function getFiltredProduits($id_materiel, $filtres = array(), $tri = null) {
          global $pdo;

          $sql = "SELECT p.*, sum(a.meilleures_ventes) as sum_meilleur_vente, m.url_marque, m.libelle_marque, "
                  . "MIN(  `prix_public` ) as prix_produit, ";
          if (!empty($filtres['technique'])) {
               if ($filtres['technique'] != "peche-mer" || $filtres['technique'] != "peche" || $filtres['technique'] != "peche-eaux-douces") {
                    $sql .= " t.type_technique, t.nom_court_technique, t.url_technique, ";
               }
          }

          $sql .= "MIN(disponibilite) as dispo_best, MAX(stock_fournisseur) as stock_best ";

          switch ($tri) {
               case 'meilleures-ventes':
                    $sql .= ", MAX(p.bestseller) as bes ";
                    break;
               case 'nouveautes':
                    $ordre_article = ", MAX(nouveau_date) as dat ";
                    break;
               case 'top':
                    $ordre_article = ", MAX(selection_tf) as sel ";
                    break;
          }

          $sql .= "FROM produit as p "
                  . "JOIN article as a ON p.id_produit=a.id_produit "
                  . "JOIN marque as m ON a.idmarque = m.idmarque ";
          //. "JOIN techniques_2012 as t ON t.id_technique=a.id_technique1 ";

          if (!empty($filtres['technique'])) {
               if ($filtres['technique'] != "peche-mer" || $filtres['technique'] != "peche" || $filtres['technique'] != "peche-eaux-douces") {
                    $sql2 = "SELECT id_technique, type_technique FROM techniques_2012 WHERE url_technique='" . $filtres['technique'] . "' ";
                    $stmt = $pdo->query($sql2);

                    $res2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $res2 = $res2[0];

                    if ($res2['type_technique'] == 'principale') {
                         $sql .= "JOIN techniques_2012 as t ON  (t.id_technique=a.id_technique1 OR t.id_technique=a.id_technique2) AND (a.id_technique1 = " . $res2['id_technique'] . " OR a.id_technique2=" . $res2['id_technique'] . ") ";
                    } else {
                         $sql .= "JOIN techniques_2012 as t ON t.id_technique=a.id_technique1 AND a.id_technique3 = " . $res2['id_technique'] . " ";
                    }
               }
          }

          $sql.= "WHERE a.id_materiel1=$id_materiel "
                  . "AND a.statut=11 AND a.actif=1 ";

          if (!empty($filtres['marque']))
               $sql .="AND m.url_marque='" . $filtres['marque'] . "' ";

          $sql .= "GROUP BY p.id_produit ";

          switch ($tri) {
               case 'pas-cher':
                    $sql .= "ORDER BY prix_produit ASC, stock_fournisseur DESC, dispo_best ASC ";
                    break;
               case 'meilleures-ventes':
                    $sql .= "ORDER BY bes DESC, stock_fournisseur DESC, dispo_best ASC ";
                    break;
               case 'nouveautes':
                    $sql .= "ORDER BY dat DESC, nouveau_date DESC, dispo_best ASC ";
                    break;
               case 'top':
                    $sql .= "ORDER BY sel DESC, stock_fournisseur DESC, dispo_best ASC ";
                    break;
               default:
                    $sql .= "ORDER BY stock_fournisseur DESC, dispo_best ASC ";
                    break;
          }

          $sql .= " Limit 5";



          try {
               $stmt = $pdo->query($sql);
          } catch (PDOException $exc) {
               echo $exc->getTraceAsString();
          }

          //echo $sql;

          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Produit($value);
          }


          return $utable;
     }

     public static function getProduitsFiltred($id_materiel, $technique, $id_marque, $url_tri) {
          global $pdo;
          if ($technique->url_technique == 'peche-mer') {
               $where_technique = " ";
          } else if ($technique->type_technique == "principale") {
               $where_technique = " AND (a.id_technique1 = " . $technique->id_technique . " OR a.id_technique2 = " . $technique->id_technique . ") ";
          } else if ($technique->type_technique == "variante") {
               $where_technique = " AND a.id_technique3 = " . $technique->id_technique . " ";
          }

          $where_marque = ($id_marque > 0) ? " AND p.idmarque=" . $id_marque : " ";
          $where_materiel = ($id_marque > 0) ? " AND a.id_materiel1=" . $id_materiel : " ";

          switch ($url_tri) {
               case 'pas-cher':
                    $order_tri = " a.prix_public ASC, a.stock_fournisseur DESC ";
                    break;
               case 'meilleures-ventes':
                    $order_tri = " a.bestseller DESC, a.stock_fournisseur DESC ";
                    break;
               case 'nouveautes':
                    $order_tri = " a.nouveau_date DESC ";
                    break;
               case 'top':
                    $order_tri = " a.selection_tf DESC, a.stock_fournisseur DESC ";
                    break;
               default:
                    $order_tri = " a.stock_fournisseur DESC ";
                    break;
          }


          $sql = "SELECT p.* $select_tri "
                  . "FROM produit as p "
                  . "INNER JOIN article as a ON p.id_produit = a.id_produit "
                  . "WHERE a.article_accessoire=0 AND a.actif=1 $where_technique $where_marque $where_materiel "
                  . "GROUP BY p.id_produit "
                  . "ORDER BY $order_tri LIMIT 5 ";

          echo $sql;
          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Produit($value);
          }

          return $utable;
     }

     public static function getProduitsByIdMateriel($id_materiel) {
          global $pdo;

          $sql = "SELECT p.*, MIN(  `prix_public` ) as prix_produit "
                  . "FROM produit as p JOIN article as a ON p.id_produit=a.id_produit "
                  . "WHERE a.id_materiel1=$id_materiel AND a.statut=11 AND a.actif=1 "
                  . "GROUP BY p.id_produit";

          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0)
               return false;

          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Produit($value);
          }
          return $utable;
     }

     public static function getAllProduits() {
          global $pdo;

          $sql = "SELECT p.*, MIN(  `prix_public` ) as prix_produit, id_technique1, id_materiel1 "
                  . "FROM produit as p JOIN article as a ON p.id_produit=a.id_produit "
                  . "WHERE a.statut=11 AND a.actif=1 "
                  . "GROUP BY p.id_produit";

          $stmt = $pdo->query($sql);
          $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($res) == 0) {
               return false;
          }


          $utable = array();

          foreach ($res as $key => $value) {
               $utable[$key] = new Produit($value);
          }
          return $utable;
     }

}
