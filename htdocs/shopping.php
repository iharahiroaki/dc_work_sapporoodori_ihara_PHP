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
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT);
        
        // 入力バリデーション
        if ($product_id === false || $num === false || $num<= 0) {
            // 無効な入力の場合、エラーメッセージを表示
             $_SESSION['error'] = '無効な入力内容です。';
            header('Location: ./shopping.php');
            exit;
        }

        try {
            // トランザクションの開始
            $dbh->beginTransaction();

            // 在庫数を確認
            $stmt = $dbh->prepare("SELECT quantity FROM product WHERE product_id = :product_id");
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $available_quantity = $stmt->fetchColumn();
            
            // ユーザーIDをセッションから取得
            $user_id = $_SESSION['user_id'];
    
            // カートに商品を追加する処理
            if ($available_quantity !== false && $available_quantity > 0) {
                // 在庫がある場合、カートに追加
                addToCart($user_id, $product_id, min($num, $available_quantity));
                $_SESSION['message'] = 'カートに商品が追加されました。';
            } else {
                // 在庫がない場合、エラーメッセージを表示
                $_SESSION['error'] = '在庫がありません。';
                header('Location: ./shopping.php');
                exit;
            }

            // トランザクションのコミット
            $dbh->commit();
        } catch (PDOException $e) {
            // トランザクションのロールバック
            $dbh->rollBack();
            echo 'カートの追加に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}

require_once('../include/view/shopping_view.php');