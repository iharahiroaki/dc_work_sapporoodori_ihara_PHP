<?php
require_once '../include/utility/common_utilites.php';

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