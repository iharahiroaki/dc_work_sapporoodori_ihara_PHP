<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// データベースに接続
require_once('../../include/model/dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('../../include/model/function.php');

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 購入した商品の情報をセッションから取得
$purchase_items = isset($_SESSION['purchase_items']) ? $_SESSION['purchase_items'] : [];

// 購入履歴をデータベースに保存する処理
if (!empty($purchase_items) && isset($_SESSION['user_id']) && is_int($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    try {
        // トランザクションの開始
        $dbh->beginTransaction();

        // SQL文の準備
        $stmt = $dbh->prepare("INSERT INTO purchase_history (user_id, product_id, quantity, purchase_date) VALUES (:user_id, :product_id, :quantity, :purchase_date)");

        // データのバリデーションと保存
        foreach ($purchase_items as $item) {
            if (isset($item['product_id'], $item['quantity']) && !is_int($item['pruduct_id']) && !is_int($item['quantity']) && $item['quantity'] <= 0) {
                throw new Exception('購入情報が不正です。');
            }

            // パラメータのバインド
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':purchase_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);

            // SQL文の実行
            $stmt->execute();
        }

        // トランザクションのコミット
        $dbh->commit();
    } catch (PDOException $e) {
        // トランザクションのロールバック
        $dbh->rollBack();
        echo '購入履歴の保存に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
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

// 購入が完了したらセッション内の購入情報を削除
unset($_SESSION['purchase_items']);

try {
    require_once('../../include/view/purchase_comp_view.php');
} catch (Exception $e) {
    echo 'viewファイルの読み込みに失敗しました。' . $e->getMessage();
}