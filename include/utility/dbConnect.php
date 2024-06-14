<?php
require_once '../include/config/const.php'; // 定数を含むファイルの読み込み

// データベース接続
function dbConnect() {
    $dsn = 'mysql:host=localhost;dbname=xb513874_bi2q3;charset=utf8';
    $user = 'xb513874_38y17';
    $pass = 'c0hqk57ipk';
    
    try {
        $dbh = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $dbh;
    } catch(PDOException $e) {
        exit('データベース接続に失敗しました: '. $e->getMessage());
    }
}