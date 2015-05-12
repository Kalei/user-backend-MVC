<?php

/**
 * Description of fournisseurTable
 *
 * @author Rovelli
 */
class fournisseurTable {

    public static function getFournisseurById($id) {
        global $pdo;

        $sql = "SELECT * FROM fournisseurs WHERE id_fournisseur=$id";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res)) return false;

        return new Fournisseur($res[0]);
    }
	public static function getFournisseurLibelleById($id) {
        global $pdo;

        $sql = "SELECT libelle_fournisseur FROM fournisseurs WHERE id_fournisseur=$id";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res)) return false;

        return $res[0]['libelle_fournisseur'];
    }

    public static function getFournisseurs() {
        global $pdo;

        $sql = "SELECT * FROM fournisseurs ";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false) {
            return false;
        }

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Fournisseur($value);
        }

        return $utable;
    }
}
?>