<?php
// セッションを開始
session_start();

// ブラウザにエラーを表示
ini_set('display_errors', "On");

// セッションからカートの商品情報を取得
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// データベースに接続
require_once('./dbConnect.php');
$dbh = dbConnect();

// function.phpの読み込み
require_once('./function.php');

// もし未ログインであれば、index.phpにリダイレクト
checkLogin();

// ログアウト処理
if (isset($_POST['logout'])) {
    logout();
}

// 商品の追加
if(isset($_POST["product_id"]) && isset($_POST["num"])) {
    $product_id = $_POST["product_id"];
    $num = $_POST["num"];
    
    // カートに商品がすでに存在するかチェック
    $existing_index = array_search($product_id, array_column($cart_items, 'product_id'));
    
    if($existing_index !== false) {
        // すでにカートに存在する場合は数量を追加
        $cart_items[$existing_index]['quantity'] += $num;
        
        // データベースのカート情報も更新する
        $stmt = $dbh->prepare("UPDATE cart SET quantity = quantity + ? WHERE product_id = ?");
        $stmt->execute([$num, $product_id]);
    } else {
        // カートに新しい商品を追加
        $product_info = getProductInfo($dbh, $product_id);
        if($product_info) {
            $product_info['quantity'] = $num;
            $cart_items[] = $product_info;
            
            // データベースにカート情報を追加
            $stmt = $dbh->prepare("INSERT INTO cart (product_id, quantity) VALUES (?, ?)");
            $stmt->execute([$product_id, $num]);
        }
    }
}

// カート情報をセッションに保存
$_SESSION['cart'] = $cart_items;

// 削除ボタンがクリックされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $product_id = $_POST['delete'];
    removeFromCart($dbh, $product_id);
    // カート情報を更新
    $cart_items = array_filter($cart_items, function ($item) use ($product_id) {
        return $item['product_id'] != $product_id;
    });
    $_SESSION['cart'] = $cart_items;
    // ページをリロードして削除後の状態を反映
    header('Location: ./cart.php');
    exit;
}

// 購入処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
    // 在庫数をチェック
    $out_of_stock = false;
    foreach ($cart_items as $item) {
        $product_info = getProductInfo($dbh, $item['product_id']);
        if ($product_info['quantity'] < $item['quantity']) {
            // 在庫が足りない場合はフラグを立ててループを抜ける
            $out_of_stock = true;
            break;
        }
    }
    
    if ($out_of_stock) {
        // 在庫がない場合の処理
        $product_name = getProductInfo($dbh, $item['product_id'])['product_name'];
        echo "<script>alert('たった今" . htmlspecialchars($product_name, ENT_QUOTES) . "の在庫がなくなりました！商品を選び直してください。');</script>";
    } else {
        // 在庫がある場合の処理
        // 在庫数の更新
        foreach ($cart_items as $item) {
            $product_info = getProductInfo($dbh, $item['product_id']);
            $new_quantity = $product_info['quantity'] - $item['quantity'];
            $stmt = $dbh->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
            $stmt->execute([$new_quantity, $item['product_id']]);
        }

        // 購入した商品の情報をセッションに保存（価格も含める）
        $purchase_items = [];
        foreach ($cart_items as $item) {
            $product_info = getProductInfo($dbh, $item['product_id']);
            $purchase_items[] = [
                'product_image' => htmlspecialchars($product_info['product_image'], ENT_QUOTES),
                'product_name' => htmlspecialchars($product_info['product_name'], ENT_QUOTES),
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product_info['price'],
            ];
        }
        $_SESSION['purchase_items'] = $purchase_items;

        // カートの中身をクリア
        $_SESSION['cart'] = [];

        header("Location: ./purchase_comp.php"); // 購入完了ページにリダイレクト
        exit;
    }
}

// 数量の変更
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // カートの商品情報を更新
    foreach ($cart_items as &$item) {
        if ($item['product_id'] === $product_id) {
            $item['quantity'] = $quantity;
            break;
        }
    }
    // リファレンスを解除
    unset($item);
    
    // カート情報をセッションに保存
    $_SESSION['cart'] = $cart_items;
    
    // ページをリロードして数量変更後の状態を反映
    header('Location: ./cart.php');
    exit;
}

// カートの合計金額を計算
$total_price = 0;
foreach ($cart_items as $item) {
    $product_info = getProductInfo($dbh, $item['product_id']);
    $total_price += $product_info['price'] * $item['quantity'];
}

include_once('./cart.php');

?>