<?php

/**
 * Description of clientTable
 *
 * @author Rovelli
 */
class clientTable {

    public static function getClientById($id) {
        global $pdo;

        $sql = "SELECT * FROM client WHERE id_client=$id";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Client($res[0]);
    }
	
	public static function getClientByIdCommande($idcommande) {
        global $pdo;

        $sql = "SELECT client.* FROM commande, client WHERE client.id_client=commande.id_client AND commande.idcommande=$idcommande";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Client($res[0]);
    }

    public static function getClientByMail($email) {
        global $pdo;

        $sql = "SELECT * FROM client WHERE email='$email'";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Client($res[0]);
    }

    public static function getIdentificationStatue($email, $password) {
        global $pdo;

        $sql = "SELECT * FROM client WHERE email='$email' && password='" . md5(md5($password)) . "'";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return false;

        return new Client($res[0]);
    }

    public static function isAllowedIp($ip) {
        global $pdo;

        $sql = "SELECT * FROM `client` WHERE `date_inscription`>= DATE_SUB(now(), INTERVAL 1 DAY) AND `date_inscription`< now() AND ip_inscription='" . $ip . "'";
        $stmt = $pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($res))
            return true;

        return false;
    }
}
