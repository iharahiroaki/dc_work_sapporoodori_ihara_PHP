<?php
// ブラウザにエラーを表示
ini_set('display_errors', "On");

require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/cart_model.php';

safeSessionStart();
$dbh = dbConnect();

if (!isset($_SESSION['username'])) {
    redirect('./index.php'); // リダイレクト関数の使用
}

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// セッションからカートの商品情報を取得
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// 商品の追加
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['num'])) {
        addItemToCart($dbh, $cart_items, $_POST["product_id"], $_POST["num"]);
    } elseif (isset($_POST['delete'])) { // 削除ボタンがクリックされた場合
        removeItemFromCart($dbh, $cart_items, $_POST['delete']);
    } elseif (isset($_POST['purchase'])) { // 購入処理
        processPurchase($dbh, $cart_items);
    } elseif (isset($_POST['update'])) { // 数量の変更
        updateCartItemQuantity($dbh, $cart_items, $_POST['product_id'], $_POST['quantity']);
    }
}

// カート情報をセッションに保存
$_SESSION['cart'] = $cart_items;

// カートの合計金額を計算
$total_price = calculateTotalPrice($dbh, $cart_items);

require_once('../include/view/cart_view.php');