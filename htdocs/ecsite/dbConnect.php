<?php
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
    } catch(PDOException $e) {
        echo '接続に失敗しました。'. $e->getMessage();
        exit();
    };

    return $dbh;
}

// 接続先　https://portfolio02.dc-itex.com/sapporoodori/0001/include/view/index_view.php
// BASIC認証
//  ユーザー名　user
//  BASIC認証パスワード　rR34bgzf