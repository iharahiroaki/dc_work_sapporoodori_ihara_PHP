<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/shopping_model.php';

safeSessionStart();
$dbh = dbConnect();

if (!isset($_SESSION['username'])) {
    redirect('./index.php'); // リダイレクト関数の使用
}

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 公開フラグが「公開」の商品のみを取得
$table = 'product';
$publicProducts = getPublicProducts($dbh, $table);

// カートに商品を追加する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'], $_POST['num'])) {
       handleAddToCart($dbh);
    }
}

require_once('../include/view/shopping_view.php');