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

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// データを取得
$table = 'product';
$allProducts = getAllData($dbh, $table);

// セッションデータの検証
if (!isset($_SESSION['user_id']) || !is_int($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    // セッションデータが不正な場合は、エラーメッセージを表示してリダイレクト
    header('Location: ./index?error=invalid_session');
    exit;
}

// ユーザーIDをセッションから取得
$user_id = $_SESSION['user_id'];

try {
    // ログインユーザーの購入履歴を取得する
    $stmt = $dbh->prepare("SELECT * FROM purchase_history WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $purchase_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // データベースエラーの場合は、エラーメッセージを表示してリダイレクト
    header('Location: ./mypage.php?error=database_error');
    exit;
}

include_once('../view/mypage_view.php');