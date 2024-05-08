<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('../model/dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('../model/function.php');

// データを取得
$table = 'product';
$allProducts = getAllData($dbh, $table);

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// ログインユーザーの購入履歴を取得する
$user_id = $_SESSION['user_id'];
$stmt = $dbh->prepare("SELECT * FROM purchase_history WHERE user_id = ?"); // $pdoではなく$dbhを使用する
$stmt->execute([$user_id]);
$purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($purchase_history);

include_once('../view/mypage_view.php');
?>