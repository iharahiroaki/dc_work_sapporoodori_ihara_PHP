<?php
require_once '../include/utility/common_utilites.php';
require_once '../include/utility/dbConnect.php';
require_once '../include/model/product_model.php';

safeSessionStart();
$dbh = dbConnect();

if (!isset($_SESSION['username'])) {
    redirect('./index.php'); // リダイレクト関数の使用
}

// データを取得
$table = 'product';
$allProducts = getAllData($dbh, $table);
// var_dump($allProducts);

// 商品の登録
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
  $success = registerProduct($dbh, $_POST, $_FILES);
  if ($success) {
      $_SESSION['success_message'] = "商品が正常に登録されました。";
  } else {
      $_SESSION['error_message'] = "商品登録に失敗しました。";
  }
  header('Location: ./product.php');
  exit;
}

// 在庫数を変更する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
  $success = updateProductQuantity($dbh, $_POST['product_id'], $_POST['quantity']);
  if ($success) {
      $_SESSION['quantity_update'] = "在庫数が正常に変更されました。";
  } else {
      $_SESSION['error_message'] = "在庫数の更新に失敗しました。";
  }
  header('Location: ./product.php');
  exit;
}

// 公開フラグの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['public_flag'])) {
  $success = updatePublicFlag($dbh, $_POST['product_id'], $_POST['public_flag']);
  if ($success) {
      $_SESSION['public_flag_update'] = "公開フラグが正常に更新されました。";
  } else {
      $_SESSION['error_message'] = "公開フラグの更新に失敗しました。";
  }
  header('Location: ./product.php');
  exit;
}

// 商品IDが渡されているかどうかを確認する
if (isset($_GET['id'])) {
  $success = deleteProduct($dbh, $_GET['id']);
  if ($success) {
      $_SESSION['product_delete'] = "商品が正常に削除されました。";
  } else {
      $_SESSION['error_message'] = "商品の削除に失敗しました。";
  }
  header('Location: ./product.php');
  exit;
}

include_once('../include/view/product_view.php');