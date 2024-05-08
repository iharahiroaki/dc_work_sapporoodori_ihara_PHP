<?php
// データを取得する関数
function getAllData($dbh, $table) {
    try {
        // ①sql文の準備
        $sql = "SELECT * FROM " . $table;
        // ②sql文の実行
        $stmt = $dbh->query($sql);
        // ③sql文の結果取り出し
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch(PDOException $e) {
        echo 'クエリの実行に失敗しました。' . $e->getMessage();
        exit();
    }
}

// 公開フラグが「公開」の商品のみを取得する関数
function getPublicProducts($dbh, $table) {
    try {
        $sql = "SELECT * FROM " . $table . " WHERE public_flag = 1";
        $stmt = $dbh->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch(PDOException $e) {
        echo 'クエリの実行に失敗しました。' . $e->getMessage();
        exit();
    }
}

// もし未ログインであれば、index.phpにリダイレクトする関数
function checkLogin () {
    if (!isset($_SESSION['username'])) {
    header("Location: ../ecsite/index.php");
    exit;
    }
}

// ログアウト処理する関数
function logout() {
    // セッションを破棄してログアウトする
    session_unset();
    session_destroy();
    // ログアウト後はログインページにリダイレクトする
    header("Location: ../ecsite/index.php");
    exit;        
}

// パスワードをハッシュ化する関数
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// ユーザー登録する関数
function registerUser($dbh, $username, $password) {
    $hashedPassword = hashPassword($password);
    try {
        $stmt = $dbh->prepare("INSERT INTO users (user_name, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
    } catch(PDOException $e) {
        echo 'ユーザー登録に失敗しました。'. $e->getMessage();
        exit();
    }
}

// パスワードを照合する関数
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// カートから商品を削除する関数
function removeFromCart($dbh, $product_id) {
    // カートから商品を削除
    $stmt = $dbh->prepare("DELETE FROM cart WHERE product_id = ?");
    $stmt->execute([$product_id]);
}

// 商品情報を取得する関数
function getProductInfo($dbh, $product_id) {
    $stmt = $dbh->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}