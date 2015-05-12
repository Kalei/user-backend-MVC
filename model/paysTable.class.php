<?php

/**
 * Description of paysTable
 *
 * @author Rovelli
 */
class paysTable {

    public static function getPaysById($id) {
        global $pdo;

        $sql = "SELECT * FROM pays WHERE id_pays=$id";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Pays($res[0]);
    }

    public static function getPaysByNom($nom) {
        global $pdo;

        $sql = "SELECT * FROM pays WHERE nom='$nom'";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Pays($res[0]);
    }

    public static function getPays() {
        global $pdo;

        $sql = "SELECT * FROM pays ORDER BY region, nom ASC";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false) {
            return false;
        }

        $utable = array();

        foreach ($res as $key => $value) {
            $utable[$key] = new Pays($value);
        }

        return $utable;
    }

}

