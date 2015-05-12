<?php

/**
 * Description of categorieTable
 *
 * @author Rovelli
 */
class categorieTable {

    public static function getCategorieById($id_categorie) {
        global $pdo;

        $sql = "SELECT * FROM categorie_tmp WHERE id_categorie='" . $id_categorie . "' ";
        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) > 0) {
            return new Categorie($res);
        }

        return false;
    }
    
    
    /**
     *  
     * @global type $pdo
     */
    public static function truncCategorieTmp() {
        global $pdo;

        //On truncate la table categorie WARNING
        $truncate = $pdo->prepare('TRUNCATE TABLE categorie_tmp');
        if ($truncate->execute()) {
            echo 'La table categorie a été vidée.<br/>';
        }

        //Réinitialisation de l'auto-incrementation
        $init_autoinc = $pdo->prepare('ALTER TABLE categorie_tmp AUTO_INCREMENT=0');
        if ($init_autoinc->execute()) {
            echo "Valeur d'auto-increment de la table catégorie remise à zéro.<br/>";
        }
    }

    /**
     * Publication de la table categorie_tmp vers categorie
     * 
     * @global type $pdo
     * @return \Categorie|boolean
     */
    public static function publishCategorieTable() {

        global $pdo;

        //On truncate la table categorie WARNING
        $truncate = $pdo->prepare('TRUNCATE TABLE categorie');
        if ($truncate->execute()) {
            echo 'La table categorie a été vidée.<br/>';
        }

        //Réinitialisation de l'auto-incrementation
        $init_autoinc = $pdo->prepare('ALTER TABLE categorie AUTO_INCREMENT=0');
        if ($init_autoinc->execute()) {
            echo "Valeur d'auto-increment de la table catégorie remise à zéro.<br/>";
        }

        //Réinitialisation de l'auto-incrementation
        $copy = $pdo->prepare('INSERT INTO categorie SELECT * FROM categorie_tmp');
        if ($copy->execute()) {
            echo "Les données ont bien été importés.<br/>";
        }
	    //On truncate la table categorie WARNING
        categorieTable::truncCategorieTmp();
    }
	public static function getCategorieByValues($id_materiel = 0, $id_marque = 0, $id_technique = 0, $url_tri = 'index', $categorie = 'categorie') {
		global $pdo;
		if(empty($id_marque))  $id_marque = 0;
		if($url_tri!=='nouveautes' && $url_tri!=='pas-cher' && $url_tri!=='meilleures-ventes' && $url_tri!=='nouveautes' && $url_tri!== 'top') $url_tri = 'index' ;
		$sql = " 
			SELECT * 
			FROM  ".$categorie."
			WHERE  id_materiel = " . $id_materiel . "
				AND id_marque = " . $id_marque . "
		 		AND id_technique = " . $id_technique ."
      			AND url_tri = '" . $url_tri."'" ;

        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Categorie($value);
        }
        return $utable[0];
    }
    
	public static function getCategoriesByValues($id_materiel = NULL, $id_marque = NULL, $id_technique = NULL, $url_tri = NULL, $limit=0, $categorie = 'categorie') {
		global $pdo;
		
		// Listes des tris autorisés ou tri par défaut
		
		$sql_select = " SELECT * FROM ".$categorie." WHERE id_categorie>0 ";
		
		// cas 1 = la materiel est définit et ce n'est pas la racine => résultat que les categories correspondant à ce matériel •••• ex url_materiel = moulinets. les résultats seront moulinets.../.../...
		// cas 2 = le materiel est 159 •••• renvoi toutes les catégories de type materiel/../../..
		// cas 3 = le materiel n'est pas définit, le résultat renverra toutes les categories materiel possibles !
		if ($id_materiel>0)	$sql_where = ' AND id_materiel = ' . $id_materiel ;
		elseif ($id_materiel<0)	$sql_where = ' AND id_materiel != ' . $id_materiel ;
		else $sql_where = ' ';
	
		// cas 1 = la marque est définit et est supérieur à 0 => résultat que les catégorie correspondant à cette marque •••• ex: cannes/shimano/...
		// cas 2 = la marque est 0  ••••  renvoi toutes les catégories de type : .../marque/...
		// cas 3 = la marque n'est pas définit et je veux précisement tout sauf zéro  ••••  ex : à partir de cannes/marque/... => les résultats marques cannes/shimano/..., cannes/daiwa/..., cannes/penn/...
		// cas 4 = la marque n'est pas définit
		if($id_marque == NULL) ;
		elseif($id_marque>=0) $sql_where .= ' AND id_marque = '.$id_marque;
		elseif($id_marque==-1) $sql_where .= ' AND id_marque>0 '; 	 	
		
		// cas 1 = la technique est définit et ce n'est pas la racine => résultat que les categories correspondant à cette technique •••• ex url_technique =jigging les résultats seront .../.../jigging/...
		// cas 2 = la technique est 1 •••• renvoi toutes les catégories de type .../.../peche-mer/...
		// cas 3 = la technique n'est pas définit, le résultat renverra toutes les categories techniques possibles !
		if ($id_technique>0)	$sql_where .= ' AND id_technique = ' . $id_technique ;
		elseif ($id_technique<0)	$sql_where .= ' AND id_technique != ' . $id_technique ;
	
		// cas 1 = on sélectionne tout
		// cas 2 = Liste des valeurs autorisées
		if($url_tri== NULL) ;
		elseif($url_tri!=='nouveautes' && $url_tri!=='pas-cher' && $url_tri!=='meilleures-ventes' && $url_tri!=='nouveautes' && $url_tri!== 'top') $url_tri = 'index' ;
		if($url_tri) $sql_where .= " AND url_tri='".$url_tri."' ";
				
		$sql_limit = ($limit != 0) ? " LIMIT 0, $limit" : "";
		$sql = $sql_select . $sql_where . $sql_limit;
		
		//echo $sql ;
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//var_dump($res);
		if (count($res) == 0)
		  return false;
		
		$utable = array();
		
		foreach ($res as $key => $value) {
		  $utable[$key] = new Categorie($value);
		}
		return $utable ;
	}
    /*public static function getCategorieByValues($id_materiel = 0, $id_marque = 0, $id_technique = 0, $url_tri = 0, $limit = false) {
        global $pdo;
        $sql_select = " SELECT * FROM categorie_tmp ";
        if ($id_materiel != 0 || $id_marque != 0 || $id_technique != 0 || $url_tri != 0)
            $sql_select .= ' WHERE ';
        $sql_where = '';
        if ($id_materiel != 0)
            $sql_where .= ($sql_where != '' ) ? ' AND id_materiel = ' . $id_materiel : ' id_materiel  = ' . $id_materiel;
        if ($id_marque != 0)
            $sql_where .= ($sql_where != '' ) ? ' AND id_marque = ' . $id_marque : ' id_marque    = ' . $id_marque;
        if ($id_technique != 0)
            $sql_where .= ($sql_where != '' ) ? ' AND id_technique = ' . $id_marque : ' id_technique = ' . $id_marque;
        if ($url_tri != 0)
            $sql_where .= ($sql_where != '' ) ? ' AND url_technique = ' . $url_technique : ' url_technique = ' . $url_technique;

        $sql_limit = ($limit != false) ? " LIMIT 0, $limit" : "";
        $sql = $sql_select . $sql_where . $sql_limit;
        //echo $sql ;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Categorie($value);
        }

        return $utable;
    }*/

    public static function getCategorieByIdMateriel($id_materiel) {
        global $pdo;

        $sql = "SELECT * FROM categorie_tmp "
                . "WHERE id_materiel=" . $id_materiel . " ";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Categorie($value);
        }

        return $utable;
    }

    public static function correctAutoIncrementId() {
        global $pdo;

        $sql = "SELECT * FROM categorie_tmp WHERE id_categorie = 0";
        $sql2 = "SELECT max(id_categorie) as last_insert FROM categorie_tmp";

        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //echo $sql;
        $stmt2 = $pdo->query($sql2);
        $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $last_insert = $res2[0]['last_insert'];

        if (count($res) == 0)
            return false;

        if (count($res2) == 0)
            return false;

        foreach ($res as $key => $value) {
            $value['id_categorie'] = ++$last_insert;
            $new_categorie = new Categorie($value, true);
            $new_categorie->save();
        }

        $sql3 = 'DELETE FROM categorie_tmp WHERE id_categorie = 0';
        $exec = $pdo->exec($sql3);

        echo 'Nombre de lignes supprimées: ' . $exec;

        $sql5 = "ALTER TABLE 'categorie_tmp' ADD PRIMARY KEY (id_categorie)";
        $exec4 = $pdo->exec($sql5);

        $sql4 = "ALTER TABLE  `categorie_tmp` CHANGE  `id_categorie`  `id_categorie` INT( 11 ) NOT NULL AUTO_INCREMENT";
        $exec2 = $pdo->exec($sql4);

        $sql5 = "ALTER TABLE categorie_tmp AUTO_INCREMENT=" . $last_insert;
        $exec3 = $pdo->exec($sql4);

        return true;
    }

}
