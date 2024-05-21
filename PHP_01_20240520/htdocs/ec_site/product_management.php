<?php
session_start();

require_once '../../include/model/product_management_model.php';
require_once '../../include/utility/common_utilities.php';


$errMessages = [];
$flg_productRegistration = -1;
$flg_stockChange = -1;
$flg_display = -1;
$errMessage = "";
$errUpdate = false;

handleProductRegistrationRequest($errMessages, $flg_productRegistration);

//在庫数変更
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $newQuantity = filter_input(INPUT_POST, 'new_quantity', FILTER_VALIDATE_INT);

    if ($productId !== false && $newQuantity !== false && $newQuantity >= 0) {
        // 在庫数を更新
        if (changeStockQuantity($productId, $newQuantity)) {
            // 成功メッセージをセッションに格納
            $_SESSION['successMessage'] = "在庫数を更新しました。";
        } else {
            // エラーメッセージをセッションに格納
            $_SESSION['errMessage'] = "在庫数の更新に失敗しました。";
        }
    } else {
        // エラーメッセージをセッションに格納
        $_SESSION['errMessage'] = "無効な入力です。";
    }
    
    // POST/Redirect/GETパターンを使用してリダイレクト
    header('Location: product_management.php');
    exit();
}

// 公開フラグの切り替えリクエストを処理する部分の直前に追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>Public flag value: ';
    print_r(isset($_POST['public_flg']) ? $_POST['public_flg'] : 'Not set');
    echo '</pre>';
}

// 公開フラグの切り替えリクエストを処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_public'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $newStatus = ($_POST['public_flg'] == '1') ? 0 : 1; // 仮の切り替えロジック

    if ($productId !== false) {
        if (togglePublicFlag($productId, $newStatus)) {
            $_SESSION['successMessage'] = "公開フラグを更新しました。";
        } else {
            $_SESSION['errMessage'] = "公開フラグの更新に失敗しました。";
        }
    } else {
        $_SESSION['errMessage'] = "無効な商品IDです。";
    }

    echo $newStatus;
    header('Location: product_management.php');
    exit();
}

$rows = db_select(); // 商品情報を取得

require_once '../../include/view/product_management_view.php';
?>
