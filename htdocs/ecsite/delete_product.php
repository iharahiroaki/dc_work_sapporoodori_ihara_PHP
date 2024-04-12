<?php
// セッションを開始
session_start();

// データベースに接続
require_once('./dbConnect.php');
$dbh = dbConnect();

// 商品IDが渡されているかどうかを確認する
if(isset($_GET['id'])) {
    $productId = $_GET['id'];

    // 商品を削除するSQL文を準備
    $stmt = $dbh->prepare("DELETE FROM product WHERE product_id = ?");
    // パラメータをバインドしてSQL文を実行
    $stmt->execute([$productId]);
    // 商品削除成功メッセージをセッションに保存
    $_SESSION['product_delete'] = "商品が正常に削除されました。";
    // 商品一覧ページにリダイレクト
    header('Location: ./product.php');
    exit;
} else {
    // 商品IDが渡されていない場合はエラーメッセージを表示するなどの処理を行う
    echo "削除する商品が指定されていません。";
}
?>