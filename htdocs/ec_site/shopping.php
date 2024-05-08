<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
// ini_set('display_errors', "On");

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
// ログアウト後にセッションIDを取得し、表示する
// $new_session_id = session_id();
// echo "ログアウト後のセッションID: " . $new_session_id;

// 公開フラグが「公開」の商品のみを取得
$table = 'product';
$publicProducts = getPublicProducts($dbh, $table);

// セッションから商品情報を取得
$products = isset($_SESSION['products']) ? $_SESSION['products'] : [];

// カートに商品を追加する関数
function addToCart($user_id, $product_id, $quantity) {
    // $dbh変数を参照する
    global $dbh;

    // セッションにカート情報がなければ初期化
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // カート内の商品を確認し、同じ商品が存在するかどうかを判定
    $existing_product_key = array_search($product_id, array_column($_SESSION['cart'], 'product_id'));
    if ($existing_product_key !== false) {
        // 同じ商品が存在する場合は、数量を追加
        $_SESSION['cart'][$existing_product_key]['quantity'] += $quantity;
    } else {
        // 同じ商品が存在しない場合は、新しく商品を追加
        $_SESSION['cart'][] = ['product_id' => $product_id, 'quantity' => $quantity];
    }
}

// カートに商品を追加する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'], $_POST['num'])) {
        $product_id = $_POST['product_id'];
        $num = $_POST['num'];
        
        // 在庫数を確認
        $stmt = $dbh->prepare("SELECT quantity FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $available_quantity = $stmt->fetchColumn();
        
        // ユーザーIDをセッションから取得
        $user_id = $_SESSION['user_id'];

        // カートに商品を追加する処理
        addToCart($user_id, $product_id, min($num, $available_quantity));
    }
}

include_once('../view/shopping_view.php');