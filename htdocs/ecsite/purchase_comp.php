<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('./dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('./function.php');

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 購入した商品の情報をセッションから取得
$purchase_items = isset($_SESSION['purchase_items']) ? $_SESSION['purchase_items'] : [];

// 購入履歴をデータベースに保存する処理
if (!empty($purchase_items)) {
    try {
        $stmt = $dbh->prepare("INSERT INTO purchase_history (user_id, product_id, quantity, purchase_date) VALUES (?, ?, ?, ?)");
        foreach ($purchase_items as $item) {
            $stmt->execute([$_SESSION['user_id'], $item['product_id'], $item['quantity'], date('Y-m-d H:i:s')]);
        }
    } catch (PDOException $e) {
        echo '購入履歴の保存に失敗しました。' . $e->getMessage();
    }
}

// 購入が完了したらセッション内の購入情報を削除
if (isset($_SESSION['purchase_items'])) {
    unset($_SESSION['purchase_items']);
}

// 購入が完了したらセッション内の購入情報を削除
unset($_SESSION['purchase_items']);
?>