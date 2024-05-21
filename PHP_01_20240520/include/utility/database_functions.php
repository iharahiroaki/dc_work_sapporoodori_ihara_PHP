<?php
require_once '../../include/config/const.php';

function get_connection() {
    try {
        $pdo = new PDO(DSN, LOGIN_USER, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        exit('Database connection error');
    }
}


?>
