<?php

class techniqueTable {

    public static function getTechniquesById($id) {
       global $pdo;
        $sql = "SELECT * FROM techniques WHERE id_technique=$id";
        //echo $sql;
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false)
            return false;

        return new Technique($res[0]);
    }

    public static function getTechniquesDesc() {
        global $pdo;
        $sql = "SELECT * FROM techniques WHERE lft!=0 AND rght!=0 ORDER BY lft DESC";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }

    public static function getTechniquesAsc() {
        global $pdo;

        $sql = "SELECT * FROM techniques WHERE lft!=0 AND rght!=0 ORDER BY lft ASC";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }

	public static function getGrandPatron() {
	   global $pdo;
	
	   $sql = "SELECT * FROM techniques WHERE id_parent=0;";
	   //echo $sql;
	   $stmt = $pdo->query($sql);
	   $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	   if ($res === false)
		  return false;
	
	   return new Technique($res[0]);
	}

	public static function getNoeuds() {

		global $pdo;
		
		$sql = "SELECT * FROM techniques WHERE id_technique IN (SELECT distinct(id_parent) FROM techniques) ORDER BY niveau ASC";
		$stmt = $pdo->query($sql);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if ($res === false) return false;
		
		$utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new NoeudTechnique($value);
        }

        return $utable;
    }

    public static function getNoeudsByLevel($level) {
        global $pdo;
        $sql = "SELECT * FROM techniques WHERE id_technique IN (SELECT distinct(id_parent) FROM techniques) AND niveau=$level";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new NoeudTechnique($value);
        }

        return $utable;
    }

    public static function getChildByIdNoeud($id) {
        global $pdo;
        $sql = "SELECT * FROM techniques WHERE id_parent=$id";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res)) return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }

    public static function getDistinctLevel() {
        global $pdo;
        $sql = "SELECT disctinct(niveau) FROM techniques";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }
	
	 public static function getAllTechniques() {
        global $pdo;

        $sql = "SELECT * FROM techniques";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }

    public static function getChildTechniquesFromParent($id_parent, $filtre = null) {
        global $pdo;

        $sql = "SELECT * FROM techniques WHERE"
                . " id_parent=" . $id_parent
                . " ORDER BY nom_court_technique";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($res) == 0)
            return false;

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Technique($value);
        }

        return $utable;
    }

    public static function getIdTechniqueFromUrl($url) {
        if ($url != null) {
            global $pdo;

            $sql = "SELECT id_technique FROM techniques WHERE url_technique='" . $url."'";
            $stmt = $pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($res) == 0)
                return false;

            return $res[0]['id_technique'];
        }
    }
}