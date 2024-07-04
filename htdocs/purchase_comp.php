<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/purchase_comp_model.php';

safeSessionStart();
$dbh = dbConnect();

if (!isset($_SESSION['username'])) {
    redirect('./index.php'); // リダイレクト関数の使用
}

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 購入した商品の情報をセッションから取得
$purchase_items = isset($_SESSION['purchase_items']) ? $_SESSION['purchase_items'] : [];

// 購入履歴をデータベースに保存する処理
if (!empty($purchase_items) && isset($_SESSION['user_id']) && is_int($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    try {
        savePurchaseHistory($dbh, $_SESSION['user_id'], $purchase_items);
    } catch (Exception $e) {
        // その他の例外処理
        $dbh->rollBack();
        echo 'エラーが発生しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

// 購入が完了したらセッション内の購入情報を削除
if (isset($_SESSION['purchase_items'])) {
    unset($_SESSION['purchase_items']);
}

require_once('../include/view/purchase_comp_view.php');