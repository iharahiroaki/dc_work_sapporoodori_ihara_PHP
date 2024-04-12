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

?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./stylesheet/cart_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="path/to/bootstrap.min.css">

    <title>毛鉤専門ショップ_ショッピングカートページ</title>
  </head>

  <body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">フライフィッシングの毛鉤専門ショップ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="./shopping.php">商品一覧</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./mypage.php">マイページ</a>
                    </li>
                    <li class="nav-item">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="nav-link">
                            <button type="submit" name="logout" class="btn btn-danger">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h1>カート</h1>

        <?php if (empty($cart_items)): ?>
            <p>カートに商品がありません。</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>金額</th>
                        <th>数量</th>
                        <th>小計</th>
                        <th>購入をやめる</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $product_info = getProductInfo($dbh, $item['product_id']); ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($product_info['product_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($product_info['product_name'], ENT_QUOTES) ?>" style="width: 100px;"></td>
                            <td><?= htmlspecialchars($product_info['product_name'], ENT_QUOTES) ?></td>
                            <td><?= number_format($product_info['price']) ?>円</td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_info['product_id'], ENT_QUOTES) ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit" name="update" class="btn btn-primary">変更</button>
                                </form>
                            </td>
                            <td><?= number_format($product_info['price'] * $item['quantity']) ?>円</td>
                            <td>
                                <form action="" method="post">
                                    <button type="submit" name="delete" value="<?= htmlspecialchars($item['product_id'], ENT_QUOTES) ?>" class="btn btn-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- カートの合計金額を表示 -->
        <div class="container text-center">
            合計金額: <?= number_format($total_price) ?>円
        </div>

        <!-- 購入するボタン -->
        <form action="" method="post" class="text-center mt-3">
            <button type="submit" name="purchase" class="btn btn-primary">購入する</button>
        </form>
    </div>

    <!-- Bootstrap JavaScript（オプション） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>