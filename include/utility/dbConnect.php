<?php
require_once '../include/config/const.php'; // 定数を含むファイルの読み込み

// データベース接続
function dbConnect() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;    
    try {
        $dbh = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $dbh;
    } catch(PDOException $e) {
        exit('データベース接続に失敗しました: '. $e->getMessage());
    }
}