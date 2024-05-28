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
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT);
        
        // 入力バリデーション
        if ($product_id === false || $num === false || $num<= 0) {
            // 無効な入力の場合、エラーメッセージを表示
            header('Location: ./shopping.php?error=invalid_input');
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
            } else {
                // 在庫がない場合、エラーメッセージを表示
                header('Location: ./shopping.php?error=out_of_stock');
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

try {
    require_once('../../include/view/shopping_view.php');
} catch (Exception $e) {
    echo 'viewファイルの読み込みに失敗しました。' . $e->getMessage();
}