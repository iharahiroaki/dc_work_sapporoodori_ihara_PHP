<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/mypage_model.php';

safeSessionStart();
$dbh = dbConnect();

if (!isset($_SESSION['username'])) {
    redirect('./index.php'); // リダイレクト関数の使用
}

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
    $purchase_history = getPurchaseHistory($dbh, $user_id);
} catch (PDOException $e) {
    // データベースエラーの場合は、エラーメッセージを表示してリダイレクト
    error_log($e->getMessage());
    header('Location: ./mypage.php?error=database_error');
    exit;
}
require_once('../include/view/mypage_view.php');