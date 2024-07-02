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

// カートに商品を追加する処理
function handleAddToCart($dbh) {
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
        error_log('カートの追加に失敗しました。' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        $_SESSION['error'] = 'カートの追加に失敗しました。';
        header('Location: ./shopping.php');
        exit;
    }

    header('Location: ./shopping.php');
    exit;
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